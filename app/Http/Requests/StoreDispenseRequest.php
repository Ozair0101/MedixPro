<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `dispense`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreDispenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'dispense_number' => ['required', 'string'],
            'patient_id' => ['nullable', 'uuid'],
            'encounter_id' => ['nullable', 'uuid'],
            'drug_order_id' => ['nullable', 'uuid'],
            'location_id' => ['required', 'uuid'],
            'dispense_type' => ['required', 'string'],
            'status' => ['nullable', 'string'],
            'screened_interactions' => ['nullable', 'boolean'],
            'screened_allergy' => ['nullable', 'boolean'],
            'screening_overridden' => ['nullable', 'boolean'],
            'override_reason' => ['nullable', 'string'],
            'dispensed_by' => ['nullable', 'uuid'],
            'dispensed_at' => ['nullable', 'date'],
            'counselled' => ['nullable', 'boolean'],
        ];
    }
}
