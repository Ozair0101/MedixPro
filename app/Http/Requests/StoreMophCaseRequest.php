<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `moph_case`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreMophCaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'encounter_id' => ['required', 'uuid'],
            'patient_id' => ['required', 'uuid'],
            'priority_condition_id' => ['nullable', 'uuid'],
            'condition_id' => ['nullable', 'uuid'],
            'register_serial' => ['required', 'integer'],
            'register_year' => ['required', 'integer'],
            'service_date' => ['required', 'date'],
            'is_new_case' => ['required', 'boolean'],
            'age_group' => ['required', 'string'],
            'sex' => ['required', 'string', 'max:1'],
            'village' => ['nullable', 'string'],
            'district_pcode' => ['nullable', 'string', 'max:6'],
            'province_pcode' => ['nullable', 'string', 'max:4'],
            'is_outside_catchment' => ['nullable', 'boolean'],
            'source_register' => ['required', 'string'],
            'recorded_at' => ['nullable', 'date'],
        ];
    }
}
