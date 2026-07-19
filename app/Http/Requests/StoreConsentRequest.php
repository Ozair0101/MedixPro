<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `consent`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreConsentRequest extends FormRequest
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
            'consent_type' => ['required', 'string'],
            'granted' => ['required', 'boolean'],
            'granted_by_self' => ['nullable', 'boolean'],
            'companion_id' => ['nullable', 'uuid'],
            'witnessed_by' => ['nullable', 'uuid'],
            'document_id' => ['nullable', 'uuid'],
            'granted_at' => ['nullable', 'date'],
            'valid_until' => ['nullable', 'date'],
            'withdrawn_at' => ['nullable', 'date'],
            'withdrawn_reason' => ['nullable', 'string'],
        ];
    }
}
