<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `patient_relation`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StorePatientRelationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'uuid'],
            'related_person_id' => ['nullable', 'uuid'],
            'name_local' => ['nullable', 'string'],
            'relationship' => ['required', 'string'],
            'phone' => ['nullable', 'string'],
            'is_emergency_contact' => ['nullable', 'boolean'],
            'valid_period' => ['nullable', 'string'],
        ];
    }
}
