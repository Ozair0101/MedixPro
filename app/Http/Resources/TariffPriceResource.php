<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `tariff_price`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class TariffPriceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tariff_id' => $this->tariff_id,
            'item_id' => $this->item_id,
            'unit_id' => $this->unit_id,
            'min_quantity' => $this->min_quantity,
            'price_type' => $this->price_type,
            'amount' => $this->amount,
            'percent' => $this->percent,
            'valid_at' => $this->valid_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'created_by' => $this->created_by,
        ];
    }
}
