<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `linen_transaction`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class LinenTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'org_unit_id' => $this->org_unit_id,
            'stock_item_id' => $this->stock_item_id,
            'transaction_type' => $this->transaction_type,
            'quantity' => $this->quantity,
            'occurred_at' => $this->occurred_at?->toIso8601String(),
            'recorded_by' => $this->recorded_by,
        ];
    }
}
