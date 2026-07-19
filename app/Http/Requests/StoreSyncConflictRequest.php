<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `sync_conflict`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreSyncConflictRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'table_name' => ['required', 'string'],
            'record_id' => ['required', 'uuid'],
            'field_name' => ['required', 'string'],
            'server_value' => ['nullable', 'array'],
            'client_value' => ['nullable', 'array'],
            'client_event_id' => ['nullable', 'uuid'],
            'status' => ['nullable', 'string'],
            'resolved_value' => ['nullable', 'array'],
            'resolved_by' => ['nullable', 'uuid'],
            'resolved_at' => ['nullable', 'date'],
            'detected_at' => ['nullable', 'date'],
        ];
    }
}
