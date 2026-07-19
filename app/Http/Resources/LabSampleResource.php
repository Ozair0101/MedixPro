<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `lab_sample`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class LabSampleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'accession_number' => $this->accession_number,
            'patient_id' => $this->patient_id,
            'encounter_id' => $this->encounter_id,
            'requisition_number' => $this->requisition_number,
            'priority' => $this->priority,
            'status' => $this->status,
            'ordered_by' => $this->ordered_by,
            'entered_at' => $this->entered_at?->toIso8601String(),
            'collected_at' => $this->collected_at?->toIso8601String(),
            'received_at' => $this->received_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
        ];
    }
}
