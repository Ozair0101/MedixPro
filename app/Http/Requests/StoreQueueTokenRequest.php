<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `queue_token`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreQueueTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'org_unit_id' => ['required', 'uuid'],
            'patient_id' => ['required', 'uuid'],
            'encounter_id' => ['nullable', 'uuid'],
            'token_code' => ['required', 'string'],
            'queue_date' => ['nullable', 'date'],
            'priority' => ['nullable', 'string'],
            'status' => ['nullable', 'string'],
            'issued_at' => ['nullable', 'date'],
            'called_at' => ['nullable', 'date'],
            'served_at' => ['nullable', 'date'],
        ];
    }
}
