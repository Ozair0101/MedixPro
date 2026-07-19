<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `patient_duplicate_candidate`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class PatientDuplicateCandidateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_a_id' => $this->patient_a_id,
            'patient_b_id' => $this->patient_b_id,
            'score' => $this->score,
            'matched_on' => $this->matched_on,
            'status' => $this->status,
            'reviewed_by' => $this->reviewed_by,
            'reviewed_at' => $this->reviewed_at?->toIso8601String(),
            'detected_at' => $this->detected_at?->toIso8601String(),
        ];
    }
}
