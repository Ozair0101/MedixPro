<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `stock_item`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class StockItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'item_code' => $this->item_code,
            'name_local' => $this->name_local,
            'name_latin' => $this->name_latin,
            'item_category' => $this->item_category,
            'ampp_id' => $this->ampp_id,
            'vmp_id' => $this->vmp_id,
            'base_unit' => $this->base_unit,
            'lot_control' => $this->lot_control,
            'expiry_control' => $this->expiry_control,
            'serialized' => $this->serialized,
            'is_controlled' => $this->is_controlled,
            'cold_chain' => $this->cold_chain,
            'reorder_level' => $this->reorder_level,
            'max_level' => $this->max_level,
            'removal_lead_days' => $this->removal_lead_days,
            'is_active' => $this->is_active,
        ];
    }
}
