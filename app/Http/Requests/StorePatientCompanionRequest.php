<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `patient_companion`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StorePatientCompanionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'uuid'],
            'name_local' => ['required', 'string'],
            'relationship' => ['required', 'string'],
            'is_mahram' => ['nullable', 'boolean'],
            'phone' => ['nullable', 'string'],
            'can_receive_results' => ['nullable', 'boolean'],
            'can_consent_on_behalf' => ['nullable', 'boolean'],
            'national_id' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
