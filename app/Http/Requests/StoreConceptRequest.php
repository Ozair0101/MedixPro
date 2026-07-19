<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `concept`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreConceptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'class_code' => ['required', 'string'],
            'datatype_code' => ['required', 'string'],
            'short_name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'is_set' => ['nullable', 'boolean'],
            'retired' => ['nullable', 'boolean'],
            'retire_reason' => ['nullable', 'string'],
            'version' => ['nullable', 'integer'],
        ];
    }
}
