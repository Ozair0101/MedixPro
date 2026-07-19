<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `imaging_report`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreImagingReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'study_id' => ['required', 'uuid'],
            'findings' => ['nullable', 'string'],
            'impression' => ['nullable', 'string'],
            'status' => ['nullable', 'string'],
            'previous_version_id' => ['nullable', 'uuid'],
            'amendment_reason' => ['nullable', 'string'],
            'reported_by' => ['nullable', 'uuid'],
            'reported_at' => ['nullable', 'date'],
            'verified_by' => ['nullable', 'uuid'],
            'verified_at' => ['nullable', 'date'],
            'rendered_document_id' => ['nullable', 'uuid'],
        ];
    }
}
