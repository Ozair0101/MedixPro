<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `condition`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreConditionRequest extends FormRequest
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
            'concept_id' => ['nullable', 'uuid'],
            'source_code' => ['nullable', 'string'],
            'source_code_system' => ['nullable', 'string'],
            'condition_text' => ['nullable', 'string'],
            'clinical_status' => ['nullable', 'string'],
            'verification_status' => ['nullable', 'string'],
            'category' => ['required', 'string'],
            'severity' => ['nullable', 'string'],
            'onset_date' => ['nullable', 'date'],
            'abatement_date' => ['nullable', 'date'],
            'recorded_at' => ['nullable', 'date'],
            'recorded_by' => ['nullable', 'uuid'],
            'previous_version_id' => ['nullable', 'uuid'],
        ];
    }
}
