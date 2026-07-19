<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `roster_requirement`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class RosterRequirementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'org_unit_id' => $this->org_unit_id,
            'shift_pattern_id' => $this->shift_pattern_id,
            'staff_category' => $this->staff_category,
            'required_count' => $this->required_count,
            'min_female_count' => $this->min_female_count,
        ];
    }
}
