<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `vitals`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreVitalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'uuid'],
            'encounter_id' => ['nullable', 'uuid'],
            'recorded_at' => ['nullable', 'date'],
            'temperature_c' => ['nullable', 'numeric'],
            'pulse_bpm' => ['nullable', 'integer'],
            'respiratory_rate' => ['nullable', 'integer'],
            'systolic_bp' => ['nullable', 'integer'],
            'diastolic_bp' => ['nullable', 'integer'],
            'spo2_percent' => ['nullable', 'integer'],
            'weight_kg' => ['nullable', 'numeric'],
            'height_cm' => ['nullable', 'numeric'],
            'muac_cm' => ['nullable', 'numeric'],
            'head_circumference_cm' => ['nullable', 'numeric'],
            'pain_score' => ['nullable', 'integer'],
            'consciousness' => ['nullable', 'string'],
            'glasgow_coma_score' => ['nullable', 'integer'],
            'recorded_by' => ['nullable', 'uuid'],
        ];
    }
}
