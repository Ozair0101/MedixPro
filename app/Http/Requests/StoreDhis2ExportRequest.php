<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `dhis2_export`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreDhis2ExportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'report_id' => ['nullable', 'uuid'],
            'period' => ['required', 'string'],
            'payload' => ['required', 'array'],
            'status' => ['nullable', 'string'],
            'generated_at' => ['nullable', 'date'],
            'sent_at' => ['nullable', 'date'],
            'response' => ['nullable', 'array'],
            'error' => ['nullable', 'string'],
        ];
    }
}
