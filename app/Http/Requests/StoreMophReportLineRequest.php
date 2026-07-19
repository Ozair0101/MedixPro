<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `moph_report_line`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreMophReportLineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'report_id' => ['required', 'uuid'],
            'section' => ['required', 'string'],
            'row_code' => ['required', 'string'],
            'row_label' => ['required', 'string'],
            'under5_male' => ['nullable', 'integer'],
            'under5_female' => ['nullable', 'integer'],
            'over5_male' => ['nullable', 'integer'],
            'over5_female' => ['nullable', 'integer'],
            'numeric_value' => ['nullable', 'numeric'],
            'text_value' => ['nullable', 'string'],
            'manual_override' => ['nullable', 'boolean'],
            'override_reason' => ['nullable', 'string'],
        ];
    }
}
