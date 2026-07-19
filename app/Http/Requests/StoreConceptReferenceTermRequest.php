<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `concept_reference_term`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreConceptReferenceTermRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code_system' => ['required', 'string'],
            'code' => ['required', 'string'],
            'display' => ['nullable', 'string'],
            'version' => ['nullable', 'string'],
            'retired' => ['nullable', 'boolean'],
        ];
    }
}
