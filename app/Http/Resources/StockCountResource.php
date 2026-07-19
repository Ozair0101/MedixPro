<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `stock_count`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class StockCountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'count_number' => $this->count_number,
            'location_id' => $this->location_id,
            'count_type' => $this->count_type,
            'status' => $this->status,
            'counted_at' => $this->counted_at?->toIso8601String(),
            'counted_by' => $this->counted_by,
            'approved_by' => $this->approved_by,
        ];
    }
}
