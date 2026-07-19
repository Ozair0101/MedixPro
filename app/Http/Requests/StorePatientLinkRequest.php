<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `patient_link`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StorePatientLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'uuid'],
            'other_patient_id' => ['required', 'uuid'],
            'link_type' => ['required', 'string'],
            'merged_by' => ['nullable', 'uuid'],
            'merged_at' => ['nullable', 'date'],
            'merge_reason' => ['required', 'string'],
            'reversed_at' => ['nullable', 'date'],
            'reversed_by' => ['nullable', 'uuid'],
        ];
    }
}
