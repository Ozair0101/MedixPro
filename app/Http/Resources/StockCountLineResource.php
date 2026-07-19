<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `stock_count_line`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class StockCountLineResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'stock_count_id' => $this->stock_count_id,
            'stock_item_id' => $this->stock_item_id,
            'stock_lot_id' => $this->stock_lot_id,
            'system_qty' => $this->system_qty,
            'counted_qty' => $this->counted_qty,
            'variance' => $this->variance,
            'variance_reason' => $this->variance_reason,
            'ledger_id' => $this->ledger_id,
        ];
    }
}
