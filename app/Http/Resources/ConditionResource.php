<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `condition`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class ConditionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'encounter_id' => $this->encounter_id,
            'concept_id' => $this->concept_id,
            'source_code' => $this->source_code,
            'source_code_system' => $this->source_code_system,
            'condition_text' => $this->condition_text,
            'clinical_status' => $this->clinical_status,
            'verification_status' => $this->verification_status,
            'category' => $this->category,
            'severity' => $this->severity,
            'onset_date' => $this->onset_date,
            'abatement_date' => $this->abatement_date,
            'recorded_at' => $this->recorded_at?->toIso8601String(),
            'recorded_by' => $this->recorded_by,
            'previous_version_id' => $this->previous_version_id,
        ];
    }
}
