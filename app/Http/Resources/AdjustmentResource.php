<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `adjustment`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class AdjustmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'account_id' => $this->account_id,
            'invoice_line_id' => $this->invoice_line_id,
            'kind' => $this->kind,
            'reason_code' => $this->reason_code,
            'amount' => $this->amount,
            'posted_at' => $this->posted_at?->toIso8601String(),
            'posted_by' => $this->posted_by,
            'approved_by' => $this->approved_by,
            'reverses_id' => $this->reverses_id,
        ];
    }
}
