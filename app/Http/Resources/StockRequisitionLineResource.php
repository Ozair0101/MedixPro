<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `stock_requisition_line`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class StockRequisitionLineResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'requisition_id' => $this->requisition_id,
            'stock_item_id' => $this->stock_item_id,
            'qty_requested' => $this->qty_requested,
            'qty_approved' => $this->qty_approved,
            'qty_fulfilled' => $this->qty_fulfilled,
            'unit_id' => $this->unit_id,
        ];
    }
}
