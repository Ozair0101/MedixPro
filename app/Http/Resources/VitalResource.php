<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `vitals`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class VitalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'encounter_id' => $this->encounter_id,
            'recorded_at' => $this->recorded_at?->toIso8601String(),
            'temperature_c' => $this->temperature_c,
            'pulse_bpm' => $this->pulse_bpm,
            'respiratory_rate' => $this->respiratory_rate,
            'systolic_bp' => $this->systolic_bp,
            'diastolic_bp' => $this->diastolic_bp,
            'spo2_percent' => $this->spo2_percent,
            'weight_kg' => $this->weight_kg,
            'height_cm' => $this->height_cm,
            'muac_cm' => $this->muac_cm,
            'head_circumference_cm' => $this->head_circumference_cm,
            'bmi' => $this->bmi,
            'pain_score' => $this->pain_score,
            'consciousness' => $this->consciousness,
            'glasgow_coma_score' => $this->glasgow_coma_score,
            'recorded_by' => $this->recorded_by,
        ];
    }
}
