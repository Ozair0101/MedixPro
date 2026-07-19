<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `encounter`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class EncounterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'visit_id' => $this->visit_id,
            'patient_id' => $this->patient_id,
            'encounter_type' => $this->encounter_type,
            'class_code' => $this->class_code,
            'status' => $this->status,
            'period' => $this->period,
            'org_unit_id' => $this->org_unit_id,
            'primary_practitioner_id' => $this->primary_practitioner_id,
            'gender_override_reason' => $this->gender_override_reason,
            'chief_complaint' => $this->chief_complaint,
            'parent_encounter_id' => $this->parent_encounter_id,
            'is_first_ever_visit' => $this->is_first_ever_visit,
            'created_at' => $this->created_at?->toIso8601String(),
            'created_by' => $this->created_by,
        ];
    }
}
