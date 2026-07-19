<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `sterilization_usage`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class SterilizationUsageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cycle_id' => $this->cycle_id,
            'set_id' => $this->set_id,
            'surgery_id' => $this->surgery_id,
            'patient_id' => $this->patient_id,
            'used_at' => $this->used_at?->toIso8601String(),
        ];
    }
}
