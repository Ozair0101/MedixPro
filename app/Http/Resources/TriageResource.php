<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `triage`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class TriageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'encounter_id' => $this->encounter_id,
            'patient_id' => $this->patient_id,
            'acuity' => $this->acuity,
            'presenting_complaint' => $this->presenting_complaint,
            'mode_of_arrival' => $this->mode_of_arrival,
            'is_medico_legal' => $this->is_medico_legal,
            'is_mass_casualty' => $this->is_mass_casualty,
            'triaged_by' => $this->triaged_by,
            'triaged_at' => $this->triaged_at?->toIso8601String(),
            'disposition' => $this->disposition,
            'disposition_at' => $this->disposition_at?->toIso8601String(),
        ];
    }
}
