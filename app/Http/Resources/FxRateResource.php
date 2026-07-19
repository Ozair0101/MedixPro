<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `fx_rate`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class FxRateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'base_currency' => $this->base_currency,
            'quote_currency' => $this->quote_currency,
            'rate_date' => $this->rate_date,
            'cash_buy' => $this->cash_buy,
            'cash_sell' => $this->cash_sell,
            'transfer_buy' => $this->transfer_buy,
            'transfer_sell' => $this->transfer_sell,
            'source' => $this->source,
            'raw_snapshot' => $this->raw_snapshot,
            'recorded_at' => $this->recorded_at?->toIso8601String(),
            'recorded_by' => $this->recorded_by,
        ];
    }
}
