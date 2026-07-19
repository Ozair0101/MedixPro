<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `queue_token`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class QueueTokenResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'org_unit_id' => $this->org_unit_id,
            'patient_id' => $this->patient_id,
            'encounter_id' => $this->encounter_id,
            'token_code' => $this->token_code,
            'queue_date' => $this->queue_date,
            'priority' => $this->priority,
            'status' => $this->status,
            'issued_at' => $this->issued_at?->toIso8601String(),
            'called_at' => $this->called_at?->toIso8601String(),
            'served_at' => $this->served_at?->toIso8601String(),
        ];
    }
}
