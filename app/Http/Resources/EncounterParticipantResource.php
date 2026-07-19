<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `encounter_participant`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class EncounterParticipantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'encounter_id' => $this->encounter_id,
            'practitioner_id' => $this->practitioner_id,
            'role' => $this->role,
            'period' => $this->period,
        ];
    }
}
