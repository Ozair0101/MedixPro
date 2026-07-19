<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `drug_order`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreDrugOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vmp_id' => ['nullable', 'uuid'],
            'drug_non_coded' => ['nullable', 'string'],
            'dose' => ['required', 'numeric'],
            'dose_unit_concept_id' => ['nullable', 'uuid'],
            'route_concept_id' => ['nullable', 'uuid'],
            'frequency_code' => ['required', 'string'],
            'duration_days' => ['nullable', 'integer'],
            'quantity_prescribed' => ['nullable', 'numeric'],
            'quantity_unit_concept_id' => ['nullable', 'uuid'],
            'as_needed' => ['nullable', 'boolean'],
            'as_needed_condition' => ['nullable', 'string'],
            'is_high_alert' => ['nullable', 'boolean'],
            'max_dose_per_period' => ['nullable', 'numeric'],
            'max_dose_period_hours' => ['nullable', 'integer'],
            'dispense_as_written' => ['nullable', 'boolean'],
        ];
    }
}
