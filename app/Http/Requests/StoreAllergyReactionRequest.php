<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `allergy_reaction`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreAllergyReactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'allergy_id' => ['required', 'uuid'],
            'severity' => ['nullable', 'string'],
            'exposure_route_concept_id' => ['nullable', 'uuid'],
            'occurred_at' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
        ];
    }
}
