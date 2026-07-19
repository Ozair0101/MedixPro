<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `encounter_diagnosis`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class EncounterDiagnosiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'encounter_id' => $this->encounter_id,
            'condition_id' => $this->condition_id,
            'diagnosis_use' => $this->diagnosis_use,
            'rank' => $this->rank,
            'is_new_case' => $this->is_new_case,
        ];
    }
}
