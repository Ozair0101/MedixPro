<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `clinical_procedure`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreClinicalProcedureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'uuid'],
            'encounter_id' => ['required', 'uuid'],
            'order_id' => ['nullable', 'uuid'],
            'concept_id' => ['required', 'uuid'],
            'status' => ['nullable', 'string'],
            'status_reason' => ['nullable', 'string'],
            'performed_period' => ['nullable', 'string'],
            'location_id' => ['nullable', 'uuid'],
            'outcome' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
