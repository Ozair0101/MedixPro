<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `patient`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StorePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mrn' => ['required', 'string'],
            'blood_group' => ['nullable', 'string'],
            'allergy_status' => ['nullable', 'string'],
            'is_unidentified' => ['nullable', 'boolean'],
            'mother_patient_id' => ['nullable', 'uuid'],
            'hide_name_on_wristband' => ['nullable', 'boolean'],
            'hide_name_on_queue' => ['nullable', 'boolean'],
            'sms_consent' => ['nullable', 'boolean'],
            'prefers_same_gender_provider' => ['nullable', 'boolean'],
            'registered_at' => ['nullable', 'date'],
            'registered_by' => ['nullable', 'uuid'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
