<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `payment`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'receipt_number' => $this->receipt_number,
            'payer_party_id' => $this->payer_party_id,
            'payer_kind' => $this->payer_kind,
            'direction' => $this->direction,
            'method' => $this->method,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'external_ref' => $this->external_ref,
            'received_at' => $this->received_at?->toIso8601String(),
            'cashier_shift_id' => $this->cashier_shift_id,
            'received_by' => $this->received_by,
            'status' => $this->status,
            'reverses_id' => $this->reverses_id,
            'approved_by' => $this->approved_by,
        ];
    }
}
