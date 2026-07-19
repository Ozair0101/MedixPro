<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `mar_slot`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class MarSlotResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'drug_order_id' => $this->drug_order_id,
            'patient_id' => $this->patient_id,
            'admission_id' => $this->admission_id,
            'scheduled_at' => $this->scheduled_at?->toIso8601String(),
            'window_start' => $this->window_start,
            'window_end' => $this->window_end,
            'is_prn' => $this->is_prn,
            'slot_status' => $this->slot_status,
            'hold_reason' => $this->hold_reason,
            'administration_id' => $this->administration_id,
        ];
    }
}
