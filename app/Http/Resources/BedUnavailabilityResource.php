<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `bed_unavailability`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class BedUnavailabilityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'bed_id' => $this->bed_id,
            'unavailable' => $this->unavailable,
            'reason' => $this->reason,
            'recorded_by' => $this->recorded_by,
        ];
    }
}
