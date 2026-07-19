<?php

namespace App\Http\Controllers;

use App\Models\Encounter;
use App\Models\Vitals;
use App\Services\EncounterService;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EncounterController extends Controller
{
    public function __construct(private readonly EncounterService $encounters) {}

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'patient_id' => ['required', 'uuid', 'exists:patient,id'],
            'encounter_type' => ['required', Rule::in([
                'registration', 'triage', 'consultation', 'nursing', 'procedure',
                'surgery', 'lab', 'radiology', 'pharmacy', 'admission', 'discharge',
                'antenatal', 'delivery', 'postnatal', 'immunization', 'follow_up',
            ])],
            'visit_id' => ['nullable', 'uuid', 'exists:visit,id'],
            'visit_type' => ['nullable', Rule::in(['OPD', 'IPD', 'ER', 'daycare', 'referral'])],
            'org_unit_id' => ['nullable', 'uuid', 'exists:org_unit,id'],
            'primary_practitioner_id' => ['nullable', 'uuid', 'exists:practitioner,id'],
            'chief_complaint' => ['nullable', 'string', 'max:500'],
            // Required only when a male provider is being assigned to a patient
            // who prefers a female one; the service enforces that rule.
            'gender_override_reason' => ['nullable', 'string', 'min:5', 'max:500'],
            'referral_source' => ['nullable', 'string', 'max:200'],
        ]);

        $encounter = $this->encounters->open($validated);

        return response()->json(['data' => $this->present($encounter)], 201);
    }

    public function show(string $id): JsonResponse
    {
        $encounter = Encounter::with(['patient.person', 'visit', 'vitals'])->findOrFail($id);

        AuditLogger::recordRead('encounter', $encounter->id, $encounter->patient_id);

        return response()->json(['data' => $this->present($encounter)]);
    }

    public function forPatient(string $patientId): JsonResponse
    {
        $encounters = Encounter::with('visit')
            ->where('patient_id', $patientId)
            ->orderByRaw('lower(period) DESC')
            ->limit(100)
            ->get();

        AuditLogger::recordRead('encounter', $patientId, $patientId);

        return response()->json([
            'data' => $encounters->map(fn (Encounter $e) => $this->present($e)),
        ]);
    }

    public function close(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['nullable', Rule::in(['finished', 'cancelled'])],
        ]);

        $encounter = Encounter::findOrFail($id);

        $closed = $this->encounters->close($encounter, $validated['status'] ?? 'finished');

        return response()->json(['data' => $this->present($closed)]);
    }

    /**
     * Record vitals against an encounter.
     *
     * The plausibility bounds live in the database as CHECK constraints, so a
     * slipped decimal is rejected there even if a future caller bypasses this
     * validation. What is validated here is duplicated deliberately — it
     * produces a readable error instead of a constraint violation.
     */
    public function recordVitals(Request $request, string $encounterId): JsonResponse
    {
        $encounter = Encounter::findOrFail($encounterId);

        $validated = $request->validate([
            'temperature_c' => ['nullable', 'numeric', 'between:25,45'],
            'pulse_bpm' => ['nullable', 'integer', 'between:20,300'],
            'respiratory_rate' => ['nullable', 'integer', 'between:4,90'],
            'systolic_bp' => ['nullable', 'integer', 'between:40,300'],
            'diastolic_bp' => ['nullable', 'integer', 'between:20,200'],
            'spo2_percent' => ['nullable', 'integer', 'between:30,100'],
            'weight_kg' => ['nullable', 'numeric', 'between:0.3,400'],
            'height_cm' => ['nullable', 'numeric', 'between:20,260'],
            'muac_cm' => ['nullable', 'numeric', 'between:5,50'],
            'pain_score' => ['nullable', 'integer', 'between:0,10'],
            'consciousness' => ['nullable', Rule::in(['alert', 'verbal', 'pain', 'unresponsive'])],
            'glasgow_coma_score' => ['nullable', 'integer', 'between:3,15'],
        ]);

        $vitals = Vitals::create(array_merge($validated, [
            'patient_id' => $encounter->patient_id,
            'encounter_id' => $encounter->id,
            'recorded_at' => now(),
            'recorded_by' => $request->user()?->id,
        ]));

        return response()->json([
            'data' => [
                'id' => $vitals->id,
                'recorded_at' => $vitals->recorded_at?->toIso8601String(),
                'bmi' => $vitals->bmi,
                // Surfaced so the UI can escalate immediately rather than
                // leaving a critical value sitting unread in a list.
                'critical_flags' => $vitals->criticalFlags(),
            ],
        ], 201);
    }

    private function present(Encounter $encounter): array
    {
        return [
            'id' => $encounter->id,
            'visit_id' => $encounter->visit_id,
            'patient_id' => $encounter->patient_id,
            'encounter_type' => $encounter->encounter_type,
            'class_code' => $encounter->class_code,
            'status' => $encounter->status,
            'chief_complaint' => $encounter->chief_complaint,
            'is_first_ever_visit' => (bool) $encounter->is_first_ever_visit,
            'gender_override_reason' => $encounter->gender_override_reason,
            'practitioner_id' => $encounter->primary_practitioner_id,
            'is_open' => $encounter->isOpen(),
        ];
    }
}
