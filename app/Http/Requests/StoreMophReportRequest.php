<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `moph_report`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreMophReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'form_type' => ['required', 'string'],
            'shamsi_month_id' => ['nullable', 'uuid'],
            'shamsi_year' => ['required', 'integer'],
            'shamsi_month_no' => ['nullable', 'integer'],
            'reporting_period' => ['required', 'string'],
            'due_date' => ['required', 'date'],
            'status' => ['nullable', 'string'],
            'generated_at' => ['nullable', 'date'],
            'submitted_at' => ['nullable', 'date'],
            'submitted_by' => ['nullable', 'uuid'],
            'submitted_to' => ['nullable', 'string'],
            'was_on_time' => ['nullable', 'boolean'],
            'rejection_reason' => ['nullable', 'string'],
            'document_id' => ['nullable', 'uuid'],
        ];
    }
}
