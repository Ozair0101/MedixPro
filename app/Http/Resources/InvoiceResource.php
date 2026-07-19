<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `invoice`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'account_id' => $this->account_id,
            'patient_id' => $this->patient_id,
            'recipient_party_id' => $this->recipient_party_id,
            'status' => $this->status,
            'currency' => $this->currency,
            'subtotal' => $this->subtotal,
            'discount_total' => $this->discount_total,
            'tax_total' => $this->tax_total,
            'rounding_adjustment' => $this->rounding_adjustment,
            'total_gross' => $this->total_gross,
            'fx_rate_id' => $this->fx_rate_id,
            'issued_at' => $this->issued_at?->toIso8601String(),
            'issued_by' => $this->issued_by,
            'due_date' => $this->due_date,
            'cancelled_reason' => $this->cancelled_reason,
        ];
    }
}
