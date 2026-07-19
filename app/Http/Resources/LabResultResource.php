<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `lab_result`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class LabResultResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'analysis_id' => $this->analysis_id,
            'analyte_concept_id' => $this->analyte_concept_id,
            'value_numeric' => $this->value_numeric,
            'value_concept_id' => $this->value_concept_id,
            'value_text' => $this->value_text,
            'unit_concept_id' => $this->unit_concept_id,
            'ref_low' => $this->ref_low,
            'ref_high' => $this->ref_high,
            'ref_text' => $this->ref_text,
            'interpretation' => $this->interpretation,
            'is_critical' => $this->is_critical,
            'result_status' => $this->result_status,
            'previous_version_id' => $this->previous_version_id,
            'correction_reason' => $this->correction_reason,
            'entered_by' => $this->entered_by,
            'entered_at' => $this->entered_at?->toIso8601String(),
        ];
    }
}
