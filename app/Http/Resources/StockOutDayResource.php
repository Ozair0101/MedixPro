<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `stock_out_day`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class StockOutDayResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'stock_item_id' => $this->stock_item_id,
            'observed_date' => $this->observed_date,
            'was_out_of_stock' => $this->was_out_of_stock,
            'qty_on_hand' => $this->qty_on_hand,
        ];
    }
}
