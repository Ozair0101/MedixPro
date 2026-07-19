<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `imaging_study`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class ImagingStudyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'accession_number' => $this->accession_number,
            'patient_id' => $this->patient_id,
            'encounter_id' => $this->encounter_id,
            'order_id' => $this->order_id,
            'modality_id' => $this->modality_id,
            'procedure_concept_id' => $this->procedure_concept_id,
            'body_site_concept_id' => $this->body_site_concept_id,
            'laterality' => $this->laterality,
            'status' => $this->status,
            'scheduled_at' => $this->scheduled_at?->toIso8601String(),
            'performed_at' => $this->performed_at?->toIso8601String(),
            'performed_by' => $this->performed_by,
            'contrast_used' => $this->contrast_used,
            'contrast_agent' => $this->contrast_agent,
            'contrast_volume_ml' => $this->contrast_volume_ml,
            'radiation_dose_mgy' => $this->radiation_dose_mgy,
            'is_repeat' => $this->is_repeat,
            'repeat_reason' => $this->repeat_reason,
            'dicom_study_uid' => $this->dicom_study_uid,
        ];
    }
}
