<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `tax_rule`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreTaxRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tax_type' => ['required', 'string'],
            'tax_category' => ['required', 'string'],
            'rate' => ['required', 'numeric'],
            'is_credit_eligible' => ['nullable', 'boolean'],
            'legal_citation' => ['nullable', 'string'],
            'valid_at' => ['required', 'string'],
        ];
    }
}
