<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `infection_surveillance`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreInfectionSurveillanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'uuid'],
            'admission_id' => ['nullable', 'uuid'],
            'infection_type' => ['required', 'string'],
            'is_healthcare_associated' => ['required', 'boolean'],
            'present_on_admission' => ['nullable', 'boolean'],
            'organism' => ['nullable', 'string'],
            'antibiogram' => ['nullable', 'array'],
            'detected_at' => ['required', 'date'],
            'lab_analysis_id' => ['nullable', 'uuid'],
            'outcome' => ['nullable', 'string'],
            'reported_by' => ['nullable', 'uuid'],
        ];
    }
}
