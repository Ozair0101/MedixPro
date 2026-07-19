<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `stock_transfer`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class StockTransferResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'transfer_number' => $this->transfer_number,
            'requisition_id' => $this->requisition_id,
            'source_location_id' => $this->source_location_id,
            'transit_location_id' => $this->transit_location_id,
            'dest_location_id' => $this->dest_location_id,
            'status' => $this->status,
            'dispatched_at' => $this->dispatched_at?->toIso8601String(),
            'dispatched_by' => $this->dispatched_by,
            'received_at' => $this->received_at?->toIso8601String(),
            'received_by' => $this->received_by,
        ];
    }
}
