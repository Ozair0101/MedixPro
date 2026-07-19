<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `diagnostic_order`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreDiagnosticOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'specimen_type_concept_id' => ['nullable', 'uuid'],
            'body_site_concept_id' => ['nullable', 'uuid'],
            'laterality' => ['nullable', 'string'],
            'clinical_history' => ['nullable', 'string'],
            'reflex_from_order_id' => ['nullable', 'uuid'],
        ];
    }
}
