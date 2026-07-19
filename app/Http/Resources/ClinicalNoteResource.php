<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `clinical_note`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class ClinicalNoteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'encounter_id' => $this->encounter_id,
            'note_type' => $this->note_type,
            'body' => $this->body,
            'status' => $this->status,
            'previous_version_id' => $this->previous_version_id,
            'amendment_reason' => $this->amendment_reason,
            'authored_by' => $this->authored_by,
            'authored_at' => $this->authored_at?->toIso8601String(),
        ];
    }
}
