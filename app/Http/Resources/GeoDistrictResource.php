<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `geo_district`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class GeoDistrictResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'pcode' => $this->pcode,
            'province_pcode' => $this->province_pcode,
            'name_latin' => $this->name_latin,
            'name_dari' => $this->name_dari,
            'name_pashto' => $this->name_pashto,
            'adm2_type' => $this->adm2_type,
            'valid_on' => $this->valid_on,
            'valid_to' => $this->valid_to,
        ];
    }
}
