<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `identifier_type`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreIdentifierTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name_local' => ['required', 'string'],
            'name_latin' => ['required', 'string'],
            'validation_regex' => ['nullable', 'string'],
            'is_unique' => ['nullable', 'boolean'],
            'normalize_digits' => ['nullable', 'boolean'],
        ];
    }
}
