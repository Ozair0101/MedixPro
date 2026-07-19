<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `infection_surveillance`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class InfectionSurveillanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'admission_id' => $this->admission_id,
            'infection_type' => $this->infection_type,
            'is_healthcare_associated' => $this->is_healthcare_associated,
            'present_on_admission' => $this->present_on_admission,
            'organism' => $this->organism,
            'antibiogram' => $this->antibiogram,
            'detected_at' => $this->detected_at?->toIso8601String(),
            'lab_analysis_id' => $this->lab_analysis_id,
            'outcome' => $this->outcome,
            'reported_by' => $this->reported_by,
        ];
    }
}
