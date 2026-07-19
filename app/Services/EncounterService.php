<?php

namespace App\Services;

use App\Models\Encounter;
use App\Models\Patient;
use App\Models\Practitioner;
use App\Models\Visit;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Opens and closes visits and encounters.
 *
 * A visit is the attendance; an encounter is one contact within it. Reusing an
 * open visit rather than creating a new one per contact is what keeps a patient
 * who is triaged, seen and then sent to the lab counted as ONE attendance
 * rather than three — which matters directly for MoPH reporting.
 */
class EncounterService
{
    /** How long an unclosed visit stays reusable for the same patient. */
    private const VISIT_REUSE_HOURS = 12;

    /**
     * Open an encounter, reusing today's visit when one is already open.
     *
     * @param  array<string, mixed>  $data
     */
    public function open(array $data): Encounter
    {
        return DB::transaction(function () use ($data) {
            $patient = Patient::with('person')->findOrFail($data['patient_id']);

            // A merged-away record must not accumulate new clinical data:
            // resolve forward so the encounter lands on the surviving chart.
            $patient = $patient->effective();

            $visit = $this->resolveVisit($patient, $data);

            $practitionerId = $data['primary_practitioner_id'] ?? null;
            $overrideReason = $data['gender_override_reason'] ?? null;

            $this->assertProviderGenderIsAcceptable(
                $patient, $practitionerId, $overrideReason
            );

            $encounter = Encounter::create([
                'visit_id' => $visit->id,
                'patient_id' => $patient->id,
                'encounter_type' => $data['encounter_type'],
                'class_code' => $data['class_code'] ?? $this->classFor($visit->visit_type),
                'status' => $data['status'] ?? 'in-progress',
                'period' => $this->range($data['started_at'] ?? now(), null),
                'org_unit_id' => $data['org_unit_id'] ?? null,
                'primary_practitioner_id' => $practitionerId,
                'gender_override_reason' => $overrideReason,
                'chief_complaint' => $data['chief_complaint'] ?? null,
                'is_first_ever_visit' => $this->isFirstEverVisit($patient->id),
                'created_by' => Auth::id(),
            ]);

            if ($overrideReason !== null) {
                // A male provider seeing a female patient is permitted in an
                // emergency, but never silently: the justification is recorded
                // against the encounter and is reviewable (ADR-010).
                AuditLogger::recordOverride(
                    'encounter', $encounter->id, $overrideReason, $patient->id
                );
            }

            AuditLogger::record(
                action: 'create',
                table: 'encounter',
                recordId: $encounter->id,
                patientId: $patient->id,
                newValues: ['type' => $encounter->encounter_type, 'visit_id' => $visit->id],
            );

            return $encounter->load('patient.person', 'visit');
        });
    }

    /**
     * Reuse an open visit for this patient, or start a new one.
     *
     * @param  array<string, mixed>  $data
     */
    private function resolveVisit(Patient $patient, array $data): Visit
    {
        if (! empty($data['visit_id'])) {
            return Visit::findOrFail($data['visit_id']);
        }

        // upper_inf(period) means the visit has no end: still open.
        //
        // The cutoff is computed in PHP rather than as `now() - interval ?`,
        // because Postgres parses an interval literal — it will not accept a
        // bind parameter there.
        $cutoff = now()->subHours(self::VISIT_REUSE_HOURS);

        $open = Visit::where('patient_id', $patient->id)
            ->whereRaw('upper_inf(period)')
            ->whereRaw('lower(period) > ?', [$cutoff])
            ->orderByRaw('lower(period) DESC')
            ->first();

        if ($open) {
            return $open;
        }

        $facilityId = $patient->facility_id;

        return Visit::create([
            'patient_id' => $patient->id,
            'visit_number' => DB::selectOne(
                'SELECT next_number(?, ?) AS n', [$facilityId, 'VISIT']
            )->n,
            'visit_type' => $data['visit_type'] ?? 'OPD',
            'period' => $this->range($data['started_at'] ?? now(), null),
            'referral_source' => $data['referral_source'] ?? null,
            'referred_from_facility' => $data['referred_from_facility'] ?? null,
        ]);
    }

    /**
     * Enforce the same-gender provider preference, with a logged override.
     *
     * Blocking outright would be unsafe — with roughly 18% of specialists
     * female, a hard rule would deny emergency care. Requiring an explicit
     * reason keeps the default protective while leaving the clinical decision
     * with the clinician.
     */
    private function assertProviderGenderIsAcceptable(
        Patient $patient,
        ?string $practitionerId,
        ?string $overrideReason,
    ): void {
        if ($practitionerId === null || ! $patient->prefers_same_gender_provider) {
            return;
        }

        $practitioner = Practitioner::find($practitionerId);

        if (! $practitioner) {
            return;
        }

        $patientGender = $patient->person?->gender ?? 'U';

        if (! $practitioner->canTreatWithoutOverride($patientGender)
            && ($overrideReason === null || trim($overrideReason) === '')) {
            throw new RuntimeException(
                'This patient is recorded as preferring a female provider. Assign a '
                .'female clinician, or record a reason for the override.'
            );
        }
    }

    /** Close an encounter. */
    public function close(Encounter $encounter, string $status = 'finished'): Encounter
    {
        return DB::transaction(function () use ($encounter, $status) {
            DB::table('encounter')->where('id', $encounter->id)->update([
                'status' => $status,
                'period' => DB::raw(
                    "tstzrange(lower(period), now(), '[)')"
                ),
            ]);

            DB::table('encounter_status_history')->insert([
                'id' => (string) Str::uuid(),
                'encounter_id' => $encounter->id,
                'status' => $status,
                'period' => '['.now()->toIso8601String().',)',
                'changed_by' => Auth::id(),
            ]);

            return $encounter->fresh();
        });
    }

    /**
     * Has this patient ever been seen here before?
     *
     * Distinct from the MoPH new-case rule, which is per-diagnosis and
     * time-windowed; this is the simpler patient-level marker.
     */
    private function isFirstEverVisit(string $patientId): bool
    {
        return ! Encounter::where('patient_id', $patientId)->exists();
    }

    private function classFor(string $visitType): string
    {
        return match ($visitType) {
            'IPD' => 'IMP',
            'ER' => 'EMER',
            'daycare' => 'SS',
            default => 'AMB',
        };
    }

    private function range(mixed $start, mixed $end): string
    {
        $lower = $start instanceof \DateTimeInterface
            ? $start->format('c')
            : (string) $start;

        return $end === null ? "[{$lower},)" : "[{$lower},{$end})";
    }
}
