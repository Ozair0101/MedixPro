<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `charge_item`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class ChargeItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'encounter_id' => $this->encounter_id,
            'account_id' => $this->account_id,
            'source_type' => $this->source_type,
            'source_id' => $this->source_id,
            'item_id' => $this->item_id,
            'quantity' => $this->quantity,
            'unit_id' => $this->unit_id,
            'tariff_id' => $this->tariff_id,
            'tariff_price_id' => $this->tariff_price_id,
            'unit_price' => $this->unit_price,
            'gross_amount' => $this->gross_amount,
            'override_amount' => $this->override_amount,
            'override_reason' => $this->override_reason,
            'status' => $this->status,
            'service_date' => $this->service_date,
            'performer_id' => $this->performer_id,
            'cost_centre_id' => $this->cost_centre_id,
            'parent_id' => $this->parent_id,
            'entered_by' => $this->entered_by,
            'entered_at' => $this->entered_at?->toIso8601String(),
        ];
    }
}
