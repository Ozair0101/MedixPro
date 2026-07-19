<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `patient_identifier`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StorePatientIdentifierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'uuid'],
            'type_code' => ['required', 'string'],
            'value' => ['required', 'string'],
            'value_raw' => ['nullable', 'string'],
            'issuing_authority' => ['nullable', 'string'],
            'issued_on' => ['nullable', 'date'],
            'expires_on' => ['nullable', 'date'],
            'is_preferred' => ['nullable', 'boolean'],
            'voided' => ['nullable', 'boolean'],
        ];
    }
}
