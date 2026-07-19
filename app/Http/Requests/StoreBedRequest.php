<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `bed`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreBedRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ward_id' => ['required', 'uuid'],
            'bed_type_id' => ['required', 'uuid'],
            'bed_number' => ['required', 'string'],
            'is_isolation' => ['nullable', 'boolean'],
            'has_oxygen' => ['nullable', 'boolean'],
            'operational_status' => ['nullable', 'string', 'max:1'],
        ];
    }
}
