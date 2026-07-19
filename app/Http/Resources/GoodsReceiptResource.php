<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `goods_receipt`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class GoodsReceiptResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'grn_number' => $this->grn_number,
            'purchase_order_id' => $this->purchase_order_id,
            'supplier_id' => $this->supplier_id,
            'location_id' => $this->location_id,
            'received_at' => $this->received_at?->toIso8601String(),
            'received_by' => $this->received_by,
            'supplier_invoice_no' => $this->supplier_invoice_no,
            'supplier_invoice_date' => $this->supplier_invoice_date,
            'match_status' => $this->match_status,
            'discrepancy_notes' => $this->discrepancy_notes,
        ];
    }
}
