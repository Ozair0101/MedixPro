<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `allergy`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class AllergyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'substance_concept_id' => $this->substance_concept_id,
            'substance_text' => $this->substance_text,
            'allergy_type' => $this->allergy_type,
            'category' => $this->category,
            'criticality' => $this->criticality,
            'clinical_status' => $this->clinical_status,
            'verification_status' => $this->verification_status,
            'onset_date' => $this->onset_date,
            'last_occurrence' => $this->last_occurrence,
            'recorded_at' => $this->recorded_at?->toIso8601String(),
            'recorded_by' => $this->recorded_by,
        ];
    }
}
