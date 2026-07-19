<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `diet_order`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class DietOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'admission_id' => $this->admission_id,
            'order_id' => $this->order_id,
            'diet_type' => $this->diet_type,
            'special_instructions' => $this->special_instructions,
            'valid_period' => $this->valid_period,
            'status' => $this->status,
        ];
    }
}
