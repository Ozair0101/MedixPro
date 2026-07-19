<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `drug_order`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class DrugOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vmp_id' => $this->vmp_id,
            'drug_non_coded' => $this->drug_non_coded,
            'dose' => $this->dose,
            'dose_unit_concept_id' => $this->dose_unit_concept_id,
            'route_concept_id' => $this->route_concept_id,
            'frequency_code' => $this->frequency_code,
            'duration_days' => $this->duration_days,
            'quantity_prescribed' => $this->quantity_prescribed,
            'quantity_unit_concept_id' => $this->quantity_unit_concept_id,
            'as_needed' => $this->as_needed,
            'as_needed_condition' => $this->as_needed_condition,
            'is_high_alert' => $this->is_high_alert,
            'max_dose_per_period' => $this->max_dose_per_period,
            'max_dose_period_hours' => $this->max_dose_period_hours,
            'dispense_as_written' => $this->dispense_as_written,
        ];
    }
}
