<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `stock_location`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class StockLocationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'code' => $this->code,
            'name_local' => $this->name_local,
            'name_latin' => $this->name_latin,
            'location_type' => $this->location_type,
            'counts_as_on_hand' => $this->counts_as_on_hand,
            'is_dispensing_point' => $this->is_dispensing_point,
            'org_unit_id' => $this->org_unit_id,
            'path' => $this->path,
            'is_active' => $this->is_active,
        ];
    }
}
