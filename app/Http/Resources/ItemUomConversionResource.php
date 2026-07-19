<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `item_uom_conversion`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class ItemUomConversionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'stock_item_id' => $this->stock_item_id,
            'unit_id' => $this->unit_id,
            'qty_in_base' => $this->qty_in_base,
            'is_purchase_default' => $this->is_purchase_default,
            'is_dispense_default' => $this->is_dispense_default,
        ];
    }
}
