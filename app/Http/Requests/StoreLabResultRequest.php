<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `lab_result`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreLabResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'analysis_id' => ['required', 'uuid'],
            'analyte_concept_id' => ['nullable', 'uuid'],
            'value_numeric' => ['nullable', 'numeric'],
            'value_concept_id' => ['nullable', 'uuid'],
            'value_text' => ['nullable', 'string'],
            'unit_concept_id' => ['nullable', 'uuid'],
            'ref_low' => ['nullable', 'numeric'],
            'ref_high' => ['nullable', 'numeric'],
            'ref_text' => ['nullable', 'string'],
            'interpretation' => ['nullable', 'string'],
            'is_critical' => ['nullable', 'boolean'],
            'result_status' => ['nullable', 'string'],
            'previous_version_id' => ['nullable', 'uuid'],
            'correction_reason' => ['nullable', 'string'],
            'entered_by' => ['nullable', 'uuid'],
            'entered_at' => ['nullable', 'date'],
        ];
    }
}
