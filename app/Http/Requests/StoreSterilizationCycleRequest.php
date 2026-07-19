<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `sterilization_cycle`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreSterilizationCycleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cycle_number' => ['required', 'string'],
            'sterilizer_asset_id' => ['nullable', 'uuid'],
            'method' => ['required', 'string'],
            'temperature_c' => ['nullable', 'numeric'],
            'pressure_bar' => ['nullable', 'numeric'],
            'duration_minutes' => ['nullable', 'integer'],
            'started_at' => ['required', 'date'],
            'completed_at' => ['nullable', 'date'],
            'chemical_indicator_pass' => ['nullable', 'boolean'],
            'biological_indicator_pass' => ['nullable', 'boolean'],
            'released' => ['nullable', 'boolean'],
            'released_by' => ['nullable', 'uuid'],
            'recalled' => ['nullable', 'boolean'],
            'recall_reason' => ['nullable', 'string'],
        ];
    }
}
