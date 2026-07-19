<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `coverage`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class CoverageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'payer_id' => $this->payer_id,
            'policy_number' => $this->policy_number,
            'kind' => $this->kind,
            'valid_period' => $this->valid_period,
            'copay_percent' => $this->copay_percent,
            'priority' => $this->priority,
        ];
    }
}
