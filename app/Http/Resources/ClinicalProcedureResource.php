<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `clinical_procedure`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class ClinicalProcedureResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'encounter_id' => $this->encounter_id,
            'order_id' => $this->order_id,
            'concept_id' => $this->concept_id,
            'status' => $this->status,
            'status_reason' => $this->status_reason,
            'performed_period' => $this->performed_period,
            'location_id' => $this->location_id,
            'outcome' => $this->outcome,
            'notes' => $this->notes,
        ];
    }
}
