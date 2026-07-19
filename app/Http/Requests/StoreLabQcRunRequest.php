<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `lab_qc_run`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreLabQcRunRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'test_id' => ['required', 'uuid'],
            'control_lot' => ['required', 'string'],
            'control_level' => ['nullable', 'string'],
            'expected_value' => ['nullable', 'numeric'],
            'expected_sd' => ['nullable', 'numeric'],
            'observed_value' => ['required', 'numeric'],
            'is_in_control' => ['required', 'boolean'],
            'westgard_rule_violated' => ['nullable', 'string'],
            'run_at' => ['nullable', 'date'],
            'run_by' => ['nullable', 'uuid'],
            'action_taken' => ['nullable', 'string'],
        ];
    }
}
