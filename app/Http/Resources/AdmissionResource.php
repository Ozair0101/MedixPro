<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `admission`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class AdmissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'visit_id' => $this->visit_id,
            'admission_number' => $this->admission_number,
            'admitted_at' => $this->admitted_at?->toIso8601String(),
            'discharged_at' => $this->discharged_at?->toIso8601String(),
            'admitting_practitioner_id' => $this->admitting_practitioner_id,
            'attending_practitioner_id' => $this->attending_practitioner_id,
            'admission_source' => $this->admission_source,
            'admission_type' => $this->admission_type,
            'discharge_outcome' => $this->discharge_outcome,
            'discharge_summary' => $this->discharge_summary,
            'death_time' => $this->death_time,
            'status' => $this->status,
        ];
    }
}
