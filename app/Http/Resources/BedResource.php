<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `bed`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class BedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ward_id' => $this->ward_id,
            'bed_type_id' => $this->bed_type_id,
            'bed_number' => $this->bed_number,
            'is_isolation' => $this->is_isolation,
            'has_oxygen' => $this->has_oxygen,
            'operational_status' => $this->operational_status,
        ];
    }
}
