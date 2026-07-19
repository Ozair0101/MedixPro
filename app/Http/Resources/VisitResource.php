<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `visit`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class VisitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'visit_number' => $this->visit_number,
            'visit_type' => $this->visit_type,
            'period' => $this->period,
            'admission_id' => $this->admission_id,
            'referral_source' => $this->referral_source,
            'referred_from_facility' => $this->referred_from_facility,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
