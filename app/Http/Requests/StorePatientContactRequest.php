<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `patient_contact`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StorePatientContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'uuid'],
            'contact_type' => ['required', 'string'],
            'value' => ['required', 'string'],
            'phone_belongs_to' => ['nullable', 'string'],
            'sms_consent' => ['nullable', 'boolean'],
            'is_primary' => ['nullable', 'boolean'],
            'voided' => ['nullable', 'boolean'],
        ];
    }
}
