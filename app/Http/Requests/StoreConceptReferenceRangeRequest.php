<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `concept_reference_range`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreConceptReferenceRangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'concept_id' => ['required', 'uuid'],
            'sex' => ['nullable', 'string', 'max:1'],
            'min_age_days' => ['nullable', 'integer'],
            'max_age_days' => ['nullable', 'integer'],
            'low_normal' => ['nullable', 'numeric'],
            'high_normal' => ['nullable', 'numeric'],
            'low_valid' => ['nullable', 'numeric'],
            'high_valid' => ['nullable', 'numeric'],
            'low_critical' => ['nullable', 'numeric'],
            'high_critical' => ['nullable', 'numeric'],
            'low_reporting' => ['nullable', 'numeric'],
            'high_reporting' => ['nullable', 'numeric'],
            'valid_period' => ['nullable', 'string'],
        ];
    }
}
