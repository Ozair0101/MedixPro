<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `geo_province`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class GeoProvinceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'pcode' => $this->pcode,
            'name_latin' => $this->name_latin,
            'name_dari' => $this->name_dari,
            'name_pashto' => $this->name_pashto,
            'region' => $this->region,
            'iso_3166_2' => $this->iso_3166_2,
            'valid_on' => $this->valid_on,
            'valid_to' => $this->valid_to,
        ];
    }
}
