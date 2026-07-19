<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\PatientLink;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Merges duplicate patient records, reversibly.
 *
 * THE RULE: nothing is deleted. The losing record is deactivated and linked to
 * the survivor, and reads resolve forward. Deleting it would break every
 * external reference holding the old MRN — printed patient cards, referral
 * letters, lab requisitions, another facility's records — and destroy the
 * audit history of a record that really did exist.
 *
 * REVERSIBILITY IS NOT OPTIONAL. Merging is a human judgement made at a busy
 * desk on partial information, and it is sometimes wrong. Fusing two people's
 * medical histories is more dangerous than the duplicate it was meant to fix,
 * so every row that moves is recorded in patient_merge_detail and the merge can
 * be undone mechanically.
 */
class PatientMergeService
{
    /**
     * Child tables whose rows follow the patient into the surviving record.
     *
     * Clinical tables are added here as later phases introduce them. Anything
     * not listed stays attached to the losing record and is reachable through
     * the merge link — visible, not lost.
     */
    private const MOVABLE = [
        'patient_identifier',
        'patient_contact',
        'patient_companion',
        'patient_relation',
    ];

    /**
     * @param  string  $survivorId  the record that continues
     * @param  string  $losingId  the record superseded by it
     */
    public function merge(string $survivorId, string $losingId, string $reason): PatientLink
    {
        if ($survivorId === $losingId) {
            throw new RuntimeException('A patient cannot be merged into itself.');
        }

        if (trim($reason) === '') {
            // An unexplained merge is indistinguishable from a mistake when
            // someone reviews it a year later.
            throw new RuntimeException('A merge reason is required.');
        }

        return DB::transaction(function () use ($survivorId, $losingId, $reason) {
            $survivor = Patient::findOrFail($survivorId);
            $losing = Patient::findOrFail($losingId);

            // Both must be live records. Merging into an already-merged record
            // would create a chain that resolves somewhere unexpected.
            if ($losing->replacedBy) {
                throw new RuntimeException(
                    "Patient {$losing->mrn} has already been merged into another record."
                );
            }
            if ($survivor->replacedBy) {
                throw new RuntimeException(
                    "Patient {$survivor->mrn} has itself been merged into another record; "
                    .'merge into the surviving record instead.'
                );
            }

            $link = PatientLink::create([
                'patient_id' => $losingId,
                'other_patient_id' => $survivorId,
                'link_type' => 'replaced_by',
                'merged_by' => Auth::id(),
                'merge_reason' => $reason,
            ]);

            // The reciprocal direction, so the survivor can enumerate what it
            // absorbed without a reverse scan.
            PatientLink::create([
                'patient_id' => $survivorId,
                'other_patient_id' => $losingId,
                'link_type' => 'replaces',
                'merged_by' => Auth::id(),
                'merge_reason' => $reason,
            ]);

            $moved = $this->moveChildRows($link->id, $losingId, $survivorId);

            // Deactivated, never deleted.
            $losing->update(['is_active' => false]);

            $this->mergeAllergyStatus($survivor, $losing);

            AuditLogger::record(
                action: 'merge',
                table: 'patient',
                recordId: $losingId,
                patientId: $survivorId,
                oldValues: ['losing_mrn' => $losing->mrn, 'is_active' => true],
                newValues: [
                    'survivor_mrn' => $survivor->mrn,
                    'reason' => $reason,
                    'rows_moved' => $moved,
                ],
            );

            return $link;
        });
    }

    /**
     * Undo a merge, restoring every moved row to its original owner.
     */
    public function unmerge(string $linkId, string $reason): void
    {
        DB::transaction(function () use ($linkId, $reason) {
            $link = PatientLink::findOrFail($linkId);

            if ($link->reversed_at !== null) {
                throw new RuntimeException('This merge has already been reversed.');
            }

            $details = DB::table('patient_merge_detail')
                ->where('patient_link_id', $linkId)->get();

            foreach ($details as $detail) {
                DB::table($detail->table_name)
                    ->where('id', $detail->record_id)
                    ->update(['patient_id' => $detail->from_patient_id]);
            }

            $now = now();

            // Reverse both directions of the link.
            PatientLink::where('patient_id', $link->patient_id)
                ->where('other_patient_id', $link->other_patient_id)
                ->whereNull('reversed_at')
                ->update(['reversed_at' => $now, 'reversed_by' => Auth::id()]);

            PatientLink::where('patient_id', $link->other_patient_id)
                ->where('other_patient_id', $link->patient_id)
                ->whereNull('reversed_at')
                ->update(['reversed_at' => $now, 'reversed_by' => Auth::id()]);

            Patient::where('id', $link->patient_id)->update(['is_active' => true]);

            AuditLogger::record(
                action: 'update',
                table: 'patient_link',
                recordId: $linkId,
                patientId: $link->patient_id,
                newValues: ['unmerged' => true, 'reason' => $reason,
                    'rows_restored' => $details->count()],
            );
        });
    }

    /**
     * Move child rows and record exactly what moved.
     *
     * The detail rows are what make unmerge mechanical rather than guesswork —
     * without them there is no way to know which of the survivor's phone
     * numbers arrived from the other record.
     */
    private function moveChildRows(string $linkId, string $losingId, string $survivorId): int
    {
        $moved = 0;

        foreach (self::MOVABLE as $table) {
            $rows = DB::table($table)->where('patient_id', $losingId)->get(['id']);

            foreach ($rows as $row) {
                DB::table('patient_merge_detail')->insert([
                    'id' => (string) Str::uuid(),
                    'patient_link_id' => $linkId,
                    'table_name' => $table,
                    'record_id' => $row->id,
                    'from_patient_id' => $losingId,
                    'to_patient_id' => $survivorId,
                ]);
            }

            $moved += DB::table($table)
                ->where('patient_id', $losingId)
                ->update(['patient_id' => $survivorId]);
        }

        return $moved;
    }

    /**
     * Combine allergy status conservatively.
     *
     * "Has allergies" always wins, and "none known" beats "unknown". Losing a
     * recorded allergy in a merge is precisely the failure that makes duplicate
     * records dangerous in the first place.
     */
    private function mergeAllergyStatus(Patient $survivor, Patient $losing): void
    {
        $rank = ['unknown' => 0, 'none_known' => 1, 'has_allergies' => 2];

        $best = $rank[$losing->allergy_status] > $rank[$survivor->allergy_status]
            ? $losing->allergy_status
            : $survivor->allergy_status;

        if ($best !== $survivor->allergy_status) {
            $survivor->update(['allergy_status' => $best]);
        }
    }
}
