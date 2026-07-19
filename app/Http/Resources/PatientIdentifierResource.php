<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `patient_identifier`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class PatientIdentifierResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'type_code' => $this->type_code,
            'value' => $this->value,
            'value_raw' => $this->value_raw,
            'issuing_authority' => $this->issuing_authority,
            'issued_on' => $this->issued_on,
            'expires_on' => $this->expires_on,
            'is_preferred' => $this->is_preferred,
            'voided' => $this->voided,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
