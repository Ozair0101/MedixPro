<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `goods_receipt_line`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class GoodsReceiptLineResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'goods_receipt_id' => $this->goods_receipt_id,
            'purchase_order_line_id' => $this->purchase_order_line_id,
            'stock_item_id' => $this->stock_item_id,
            'stock_lot_id' => $this->stock_lot_id,
            'batch_no' => $this->batch_no,
            'expiry_date' => $this->expiry_date,
            'qty_received' => $this->qty_received,
            'qty_accepted' => $this->qty_accepted,
            'qty_rejected' => $this->qty_rejected,
            'rejection_reason' => $this->rejection_reason,
            'unit_id' => $this->unit_id,
            'unit_cost' => $this->unit_cost,
            'ledger_id' => $this->ledger_id,
        ];
    }
}
