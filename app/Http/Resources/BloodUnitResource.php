<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `blood_unit`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class BloodUnitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'donation_id' => $this->donation_id,
            'unit_number' => $this->unit_number,
            'component' => $this->component,
            'blood_group' => $this->blood_group,
            'volume_ml' => $this->volume_ml,
            'prepared_at' => $this->prepared_at?->toIso8601String(),
            'expires_at' => $this->expires_at?->toIso8601String(),
            'storage_location_id' => $this->storage_location_id,
            'status' => $this->status,
            'discard_reason' => $this->discard_reason,
        ];
    }
}
