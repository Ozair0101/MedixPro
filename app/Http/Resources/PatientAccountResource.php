<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `patient_account`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class PatientAccountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'account_number' => $this->account_number,
            'patient_id' => $this->patient_id,
            'encounter_id' => $this->encounter_id,
            'admission_id' => $this->admission_id,
            'kind' => $this->kind,
            'status' => $this->status,
            'billing_status' => $this->billing_status,
            'guarantor_party_id' => $this->guarantor_party_id,
            'service_period' => $this->service_period,
            'currency' => $this->currency,
            'opened_at' => $this->opened_at?->toIso8601String(),
            'closed_at' => $this->closed_at?->toIso8601String(),
        ];
    }
}
