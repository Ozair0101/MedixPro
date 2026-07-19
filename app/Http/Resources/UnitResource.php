<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `unit`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class UnitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uom_category_id' => $this->uom_category_id,
            'code' => $this->code,
            'name_local' => $this->name_local,
            'name_latin' => $this->name_latin,
            'factor_to_reference' => $this->factor_to_reference,
            'is_reference' => $this->is_reference,
            'rounding_precision' => $this->rounding_precision,
            'rounding_mode' => $this->rounding_mode,
        ];
    }
}
