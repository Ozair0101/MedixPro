<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `stock_balance`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class StockBalanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'stock_item_id' => $this->stock_item_id,
            'location_id' => $this->location_id,
            'stock_lot_id' => $this->stock_lot_id,
            'qty_on_hand' => $this->qty_on_hand,
            'qty_allocated' => $this->qty_allocated,
            'qty_available' => $this->qty_available,
            'moving_avg_cost' => $this->moving_avg_cost,
            'version' => $this->version,
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
