<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `patient_contact`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class PatientContactResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'contact_type' => $this->contact_type,
            'value' => $this->value,
            'phone_belongs_to' => $this->phone_belongs_to,
            'sms_consent' => $this->sms_consent,
            'is_primary' => $this->is_primary,
            'voided' => $this->voided,
        ];
    }
}
