<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `sterilization_usage`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreSterilizationUsageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cycle_id' => ['required', 'uuid'],
            'set_id' => ['required', 'uuid'],
            'surgery_id' => ['nullable', 'uuid'],
            'patient_id' => ['nullable', 'uuid'],
            'used_at' => ['nullable', 'date'],
        ];
    }
}
