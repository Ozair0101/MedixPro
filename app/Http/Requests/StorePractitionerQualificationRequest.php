<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `practitioner_qualification`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StorePractitionerQualificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'practitioner_id' => ['required', 'uuid'],
            'qualification' => ['required', 'string'],
            'issuing_body' => ['nullable', 'string'],
            'licence_number' => ['nullable', 'string'],
            'valid_period' => ['required', 'string'],
        ];
    }
}
