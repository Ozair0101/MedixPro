<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `patient_relation`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class PatientRelationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'related_person_id' => $this->related_person_id,
            'name_local' => $this->name_local,
            'relationship' => $this->relationship,
            'phone' => $this->phone,
            'is_emergency_contact' => $this->is_emergency_contact,
            'valid_period' => $this->valid_period,
        ];
    }
}
