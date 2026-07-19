<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `moph_case`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class MophCaseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'encounter_id' => $this->encounter_id,
            'patient_id' => $this->patient_id,
            'priority_condition_id' => $this->priority_condition_id,
            'condition_id' => $this->condition_id,
            'register_serial' => $this->register_serial,
            'register_year' => $this->register_year,
            'service_date' => $this->service_date,
            'is_new_case' => $this->is_new_case,
            'age_group' => $this->age_group,
            'sex' => $this->sex,
            'village' => $this->village,
            'district_pcode' => $this->district_pcode,
            'province_pcode' => $this->province_pcode,
            'is_outside_catchment' => $this->is_outside_catchment,
            'source_register' => $this->source_register,
            'recorded_at' => $this->recorded_at?->toIso8601String(),
        ];
    }
}
