<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `virtual_product_ingredient`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class VirtualProductIngredientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'vmp_id' => $this->vmp_id,
            'substance_id' => $this->substance_id,
            'strength_num_value' => $this->strength_num_value,
            'strength_num_unit' => $this->strength_num_unit,
            'strength_den_value' => $this->strength_den_value,
            'strength_den_unit' => $this->strength_den_unit,
        ];
    }
}
