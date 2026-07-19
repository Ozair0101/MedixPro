<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `appointment`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'uuid'],
            'org_unit_id' => ['required', 'uuid'],
            'practitioner_id' => ['nullable', 'uuid'],
            'slot' => ['required', 'string'],
            'status' => ['nullable', 'string'],
            'reason' => ['nullable', 'string'],
            'booked_at' => ['nullable', 'date'],
            'booked_by' => ['nullable', 'uuid'],
            'cancelled_reason' => ['nullable', 'string'],
            'rescheduled_to_id' => ['nullable', 'uuid'],
            'encounter_id' => ['nullable', 'uuid'],
        ];
    }
}
