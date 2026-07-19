<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `blood_transfusion`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class BloodTransfusionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'encounter_id' => $this->encounter_id,
            'blood_unit_id' => $this->blood_unit_id,
            'crossmatch_id' => $this->crossmatch_id,
            'started_at' => $this->started_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'volume_transfused_ml' => $this->volume_transfused_ml,
            'administered_by' => $this->administered_by,
            'verified_by' => $this->verified_by,
            'status' => $this->status,
        ];
    }
}
