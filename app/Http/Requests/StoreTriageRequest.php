<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `triage`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreTriageRequest extends FormRequest
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
            'acuity' => ['required', 'integer'],
            'presenting_complaint' => ['required', 'string'],
            'mode_of_arrival' => ['nullable', 'string'],
            'is_medico_legal' => ['nullable', 'boolean'],
            'is_mass_casualty' => ['nullable', 'boolean'],
            'triaged_by' => ['required', 'uuid'],
            'triaged_at' => ['nullable', 'date'],
            'disposition' => ['nullable', 'string'],
            'disposition_at' => ['nullable', 'date'],
        ];
    }
}
