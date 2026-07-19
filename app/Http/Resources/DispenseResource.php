<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `dispense`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class DispenseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'dispense_number' => $this->dispense_number,
            'patient_id' => $this->patient_id,
            'encounter_id' => $this->encounter_id,
            'drug_order_id' => $this->drug_order_id,
            'location_id' => $this->location_id,
            'dispense_type' => $this->dispense_type,
            'status' => $this->status,
            'screened_interactions' => $this->screened_interactions,
            'screened_allergy' => $this->screened_allergy,
            'screening_overridden' => $this->screening_overridden,
            'override_reason' => $this->override_reason,
            'dispensed_by' => $this->dispensed_by,
            'dispensed_at' => $this->dispensed_at?->toIso8601String(),
            'counselled' => $this->counselled,
        ];
    }
}
