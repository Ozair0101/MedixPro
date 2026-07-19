<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `payment_allocation`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class PaymentAllocationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'payment_id' => $this->payment_id,
            'target_type' => $this->target_type,
            'target_id' => $this->target_id,
            'amount' => $this->amount,
            'allocated_at' => $this->allocated_at?->toIso8601String(),
            'delinked_at' => $this->delinked_at?->toIso8601String(),
            'delinked_by' => $this->delinked_by,
        ];
    }
}
