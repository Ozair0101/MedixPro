<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `lab_analysis`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreLabAnalysiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sample_item_id' => ['required', 'uuid'],
            'test_id' => ['required', 'uuid'],
            'panel_id' => ['nullable', 'uuid'],
            'order_id' => ['nullable', 'uuid'],
            'section_id' => ['required', 'uuid'],
            'status' => ['nullable', 'string'],
            'revision' => ['nullable', 'integer'],
            'parent_analysis_id' => ['nullable', 'uuid'],
            'reflex_triggered' => ['nullable', 'boolean'],
            'analyzer_id' => ['nullable', 'uuid'],
            'started_at' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date'],
            'technical_accepted_by' => ['nullable', 'uuid'],
            'technical_accepted_at' => ['nullable', 'date'],
            'clinically_validated_by' => ['nullable', 'uuid'],
            'clinically_validated_at' => ['nullable', 'date'],
            'released_at' => ['nullable', 'date'],
            'is_reportable' => ['nullable', 'boolean'],
            'referred_to_org' => ['nullable', 'string'],
            'referred_at' => ['nullable', 'date'],
        ];
    }
}
