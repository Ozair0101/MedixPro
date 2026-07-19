<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `clinical_note`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreClinicalNoteRequest extends FormRequest
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
            'note_type' => ['required', 'string'],
            'body' => ['required', 'string'],
            'status' => ['nullable', 'string'],
            'previous_version_id' => ['nullable', 'uuid'],
            'amendment_reason' => ['nullable', 'string'],
            'authored_by' => ['required', 'uuid'],
            'authored_at' => ['nullable', 'date'],
        ];
    }
}
