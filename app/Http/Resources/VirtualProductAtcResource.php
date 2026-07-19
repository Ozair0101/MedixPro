<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `virtual_product_atc`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class VirtualProductAtcResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'vmp_id' => $this->vmp_id,
            'atc_code' => $this->atc_code,
            'ddd_value' => $this->ddd_value,
            'ddd_unit' => $this->ddd_unit,
        ];
    }
}
