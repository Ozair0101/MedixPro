<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `admission`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreAdmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'uuid'],
            'visit_id' => ['required', 'uuid'],
            'admission_number' => ['required', 'string'],
            'admitted_at' => ['required', 'date'],
            'discharged_at' => ['nullable', 'date'],
            'admitting_practitioner_id' => ['nullable', 'uuid'],
            'attending_practitioner_id' => ['nullable', 'uuid'],
            'admission_source' => ['nullable', 'string'],
            'admission_type' => ['nullable', 'string'],
            'discharge_outcome' => ['nullable', 'string'],
            'discharge_summary' => ['nullable', 'string'],
            'death_time' => ['nullable', 'date'],
            'status' => ['nullable', 'string'],
        ];
    }
}
