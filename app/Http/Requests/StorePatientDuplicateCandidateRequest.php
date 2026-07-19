<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `patient_duplicate_candidate`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StorePatientDuplicateCandidateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_a_id' => ['required', 'uuid'],
            'patient_b_id' => ['required', 'uuid'],
            'score' => ['required', 'numeric'],
            'matched_on' => ['required', 'string'],
            'status' => ['nullable', 'string'],
            'reviewed_by' => ['nullable', 'uuid'],
            'reviewed_at' => ['nullable', 'date'],
            'detected_at' => ['nullable', 'date'],
        ];
    }
}
