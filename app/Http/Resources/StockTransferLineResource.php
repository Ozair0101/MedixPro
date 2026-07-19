<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `stock_transfer_line`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class StockTransferLineResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'transfer_id' => $this->transfer_id,
            'stock_item_id' => $this->stock_item_id,
            'stock_lot_id' => $this->stock_lot_id,
            'qty_dispatched' => $this->qty_dispatched,
            'qty_received' => $this->qty_received,
            'unit_id' => $this->unit_id,
        ];
    }
}
