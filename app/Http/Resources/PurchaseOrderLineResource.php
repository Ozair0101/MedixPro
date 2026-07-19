<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `purchase_order_line`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class PurchaseOrderLineResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'purchase_order_id' => $this->purchase_order_id,
            'stock_item_id' => $this->stock_item_id,
            'qty_ordered' => $this->qty_ordered,
            'qty_received' => $this->qty_received,
            'unit_id' => $this->unit_id,
            'unit_cost' => $this->unit_cost,
            'discount' => $this->discount,
            'tax' => $this->tax,
            'line_total' => $this->line_total,
        ];
    }
}
