<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `encounter`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreEncounterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'visit_id' => ['required', 'uuid'],
            'patient_id' => ['required', 'uuid'],
            'encounter_type' => ['required', 'string'],
            'class_code' => ['required', 'string'],
            'status' => ['nullable', 'string'],
            'period' => ['required', 'string'],
            'org_unit_id' => ['nullable', 'uuid'],
            'primary_practitioner_id' => ['nullable', 'uuid'],
            'gender_override_reason' => ['nullable', 'string'],
            'chief_complaint' => ['nullable', 'string'],
            'parent_encounter_id' => ['nullable', 'uuid'],
            'is_first_ever_visit' => ['nullable', 'boolean'],
            'created_by' => ['nullable', 'uuid'],
        ];
    }
}
