<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `surgery`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreSurgeryRequest extends FormRequest
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
            'procedure_id' => ['nullable', 'uuid'],
            'theatre_location_id' => ['nullable', 'uuid'],
            'scheduled' => ['required', 'string'],
            'actual' => ['nullable', 'string'],
            'urgency' => ['required', 'string'],
            'asa_grade' => ['nullable', 'integer'],
            'anaesthesia_type' => ['nullable', 'string'],
            'checklist_signin_at' => ['nullable', 'date'],
            'checklist_timeout_at' => ['nullable', 'date'],
            'checklist_signout_at' => ['nullable', 'date'],
            'estimated_blood_loss_ml' => ['nullable', 'integer'],
            'operative_note' => ['nullable', 'string'],
            'status' => ['nullable', 'string'],
            'cancellation_reason' => ['nullable', 'string'],
        ];
    }
}
