<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `practitioner`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StorePractitionerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'person_id' => ['nullable', 'uuid'],
            'user_id' => ['nullable', 'uuid'],
            'moph_staff_code' => ['nullable', 'string'],
            'name_local' => ['required', 'string'],
            'name_latin' => ['required', 'string'],
            'gender' => ['required', 'string', 'max:1'],
            'practitioner_type' => ['required', 'string'],
            'primary_org_unit_id' => ['nullable', 'uuid'],
            'phone' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
