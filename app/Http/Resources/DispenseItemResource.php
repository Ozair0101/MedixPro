<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `dispense_item`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class DispenseItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'dispense_id' => $this->dispense_id,
            'stock_item_id' => $this->stock_item_id,
            'stock_lot_id' => $this->stock_lot_id,
            'quantity' => $this->quantity,
            'unit_id' => $this->unit_id,
            'base_quantity' => $this->base_quantity,
            'unit_price' => $this->unit_price,
            'ledger_id' => $this->ledger_id,
        ];
    }
}
