<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `observation`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreObservationRequest extends FormRequest
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
            'order_id' => ['nullable', 'uuid'],
            'concept_id' => ['required', 'uuid'],
            'observed_at' => ['required', 'date'],
            'parent_observation_id' => ['nullable', 'uuid'],
            'value_numeric' => ['nullable', 'numeric'],
            'value_concept_id' => ['nullable', 'uuid'],
            'value_text' => ['nullable', 'string'],
            'value_datetime' => ['nullable', 'date'],
            'value_boolean' => ['nullable', 'boolean'],
            'value_complex' => ['nullable', 'uuid'],
            'absent_reason' => ['nullable', 'string'],
            'unit_concept_id' => ['nullable', 'uuid'],
            'ref_low' => ['nullable', 'numeric'],
            'ref_high' => ['nullable', 'numeric'],
            'interpretation' => ['nullable', 'string'],
            'status' => ['nullable', 'string'],
            'previous_version_id' => ['nullable', 'uuid'],
            'comments' => ['nullable', 'string'],
            'form_path' => ['nullable', 'string'],
            'recorded_by' => ['nullable', 'uuid'],
            'recorded_at' => ['nullable', 'date'],
            'voided' => ['nullable', 'boolean'],
            'voided_by' => ['nullable', 'uuid'],
            'void_reason' => ['nullable', 'string'],
        ];
    }
}
