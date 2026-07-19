<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `meal_service`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class MealServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ward_id' => $this->ward_id,
            'service_date' => $this->service_date,
            'meal' => $this->meal,
            'diet_type' => $this->diet_type,
            'portions_required' => $this->portions_required,
            'portions_served' => $this->portions_served,
        ];
    }
}
