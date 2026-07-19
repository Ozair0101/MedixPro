<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `patient_companion`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class PatientCompanionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'name_local' => $this->name_local,
            'relationship' => $this->relationship,
            'is_mahram' => $this->is_mahram,
            'phone' => $this->phone,
            'can_receive_results' => $this->can_receive_results,
            'can_consent_on_behalf' => $this->can_consent_on_behalf,
            'national_id' => $this->national_id,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
