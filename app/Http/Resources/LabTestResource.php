<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `lab_test`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class LabTestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'concept_id' => $this->concept_id,
            'section_id' => $this->section_id,
            'name_local' => $this->name_local,
            'name_latin' => $this->name_latin,
            'loinc_code' => $this->loinc_code,
            'specimen_type_concept_id' => $this->specimen_type_concept_id,
            'container_type' => $this->container_type,
            'minimum_volume_ml' => $this->minimum_volume_ml,
            'result_datatype' => $this->result_datatype,
            'unit_concept_id' => $this->unit_concept_id,
            'significant_digits' => $this->significant_digits,
            'tat_average_minutes' => $this->tat_average_minutes,
            'tat_warning_minutes' => $this->tat_warning_minutes,
            'tat_max_minutes' => $this->tat_max_minutes,
            'is_orderable' => $this->is_orderable,
            'is_reportable' => $this->is_reportable,
            'requires_fasting' => $this->requires_fasting,
            'billable_item_id' => $this->billable_item_id,
            'valid_period' => $this->valid_period,
        ];
    }
}
