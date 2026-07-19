<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `clinic_schedule`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class ClinicScheduleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'org_unit_id' => $this->org_unit_id,
            'practitioner_id' => $this->practitioner_id,
            'weekday' => $this->weekday,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'slot_minutes' => $this->slot_minutes,
            'max_walk_ins' => $this->max_walk_ins,
            'valid_period' => $this->valid_period,
        ];
    }
}
