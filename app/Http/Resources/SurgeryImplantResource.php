<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `surgery_implant`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class SurgeryImplantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'surgery_id' => $this->surgery_id,
            'stock_item_id' => $this->stock_item_id,
            'stock_lot_id' => $this->stock_lot_id,
            'serial_number' => $this->serial_number,
            'description' => $this->description,
            'quantity' => $this->quantity,
        ];
    }
}
