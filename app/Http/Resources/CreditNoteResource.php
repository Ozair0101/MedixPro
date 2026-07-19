<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `credit_note`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class CreditNoteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'credit_note_number' => $this->credit_note_number,
            'invoice_id' => $this->invoice_id,
            'reason_code' => $this->reason_code,
            'total_amount' => $this->total_amount,
            'issued_at' => $this->issued_at?->toIso8601String(),
            'issued_by' => $this->issued_by,
            'approved_by' => $this->approved_by,
        ];
    }
}
