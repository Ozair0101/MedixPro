<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `patient_complaint`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class PatientComplaintResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'complaint_number' => $this->complaint_number,
            'patient_id' => $this->patient_id,
            'complainant_name' => $this->complainant_name,
            'category' => $this->category,
            'description' => $this->description,
            'received_at' => $this->received_at?->toIso8601String(),
            'org_unit_id' => $this->org_unit_id,
            'status' => $this->status,
            'resolution' => $this->resolution,
            'resolved_at' => $this->resolved_at?->toIso8601String(),
        ];
    }
}
