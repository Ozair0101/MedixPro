<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `observation`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class ObservationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'encounter_id' => $this->encounter_id,
            'order_id' => $this->order_id,
            'concept_id' => $this->concept_id,
            'observed_at' => $this->observed_at?->toIso8601String(),
            'parent_observation_id' => $this->parent_observation_id,
            'value_numeric' => $this->value_numeric,
            'value_concept_id' => $this->value_concept_id,
            'value_text' => $this->value_text,
            'value_datetime' => $this->value_datetime,
            'value_boolean' => $this->value_boolean,
            'value_complex' => $this->value_complex,
            'absent_reason' => $this->absent_reason,
            'unit_concept_id' => $this->unit_concept_id,
            'ref_low' => $this->ref_low,
            'ref_high' => $this->ref_high,
            'interpretation' => $this->interpretation,
            'status' => $this->status,
            'previous_version_id' => $this->previous_version_id,
            'comments' => $this->comments,
            'form_path' => $this->form_path,
            'recorded_by' => $this->recorded_by,
            'recorded_at' => $this->recorded_at?->toIso8601String(),
            'voided' => $this->voided,
            'voided_by' => $this->voided_by,
            'void_reason' => $this->void_reason,
        ];
    }
}
