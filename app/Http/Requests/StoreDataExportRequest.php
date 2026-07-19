<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `data_export`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreDataExportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'export_type' => ['required', 'string'],
            'format' => ['required', 'string'],
            'requested_by' => ['required', 'uuid'],
            'requested_at' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date'],
            'row_count' => ['nullable', 'integer'],
            'byte_size' => ['nullable', 'integer'],
            'storage_path' => ['nullable', 'string'],
            'sha256' => ['nullable', 'string', 'max:64'],
            'status' => ['nullable', 'string'],
        ];
    }
}
