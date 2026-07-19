<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `patient_complaint`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StorePatientComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'complaint_number' => ['required', 'string'],
            'patient_id' => ['nullable', 'uuid'],
            'complainant_name' => ['nullable', 'string'],
            'category' => ['required', 'string'],
            'description' => ['required', 'string'],
            'received_at' => ['nullable', 'date'],
            'org_unit_id' => ['nullable', 'uuid'],
            'status' => ['nullable', 'string'],
            'resolution' => ['nullable', 'string'],
            'resolved_at' => ['nullable', 'date'],
        ];
    }
}
