<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `consent`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class ConsentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'encounter_id' => $this->encounter_id,
            'consent_type' => $this->consent_type,
            'granted' => $this->granted,
            'granted_by_self' => $this->granted_by_self,
            'companion_id' => $this->companion_id,
            'witnessed_by' => $this->witnessed_by,
            'document_id' => $this->document_id,
            'granted_at' => $this->granted_at?->toIso8601String(),
            'valid_until' => $this->valid_until,
            'withdrawn_at' => $this->withdrawn_at?->toIso8601String(),
            'withdrawn_reason' => $this->withdrawn_reason,
        ];
    }
}
