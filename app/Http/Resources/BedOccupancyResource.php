<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `bed_occupancy`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class BedOccupancyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'bed_id' => $this->bed_id,
            'admission_id' => $this->admission_id,
            'patient_id' => $this->patient_id,
            'occupied' => $this->occupied,
            'status' => $this->status,
            'cancelled' => $this->cancelled,
            'ward_id' => $this->ward_id,
            'ward_sex_policy' => $this->ward_sex_policy,
            'patient_sex' => $this->patient_sex,
        ];
    }
}
