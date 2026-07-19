<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `stock_lot`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class StockLotResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'stock_item_id' => $this->stock_item_id,
            'lot_number' => $this->lot_number,
            'serial_number' => $this->serial_number,
            'expiry_date' => $this->expiry_date,
            'removal_date' => $this->removal_date,
            'manufacture_date' => $this->manufacture_date,
            'gtin' => $this->gtin,
            'supplier_id' => $this->supplier_id,
            'lot_status' => $this->lot_status,
        ];
    }
}
