<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `practitioner`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class PractitionerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'person_id' => $this->person_id,
            'user_id' => $this->user_id,
            'moph_staff_code' => $this->moph_staff_code,
            'name_local' => $this->name_local,
            'name_latin' => $this->name_latin,
            'gender' => $this->gender,
            'practitioner_type' => $this->practitioner_type,
            'primary_org_unit_id' => $this->primary_org_unit_id,
            'phone' => $this->phone,
            'is_active' => $this->is_active,
        ];
    }
}
