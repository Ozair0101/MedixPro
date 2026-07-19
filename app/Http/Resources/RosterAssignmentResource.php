<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `roster_assignment`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class RosterAssignmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'org_unit_id' => $this->org_unit_id,
            'shift_pattern_id' => $this->shift_pattern_id,
            'shift' => $this->shift,
            'assignment_type' => $this->assignment_type,
            'status' => $this->status,
        ];
    }
}
