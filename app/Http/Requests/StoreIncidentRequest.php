<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `incident`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreIncidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'incident_number' => ['required', 'string'],
            'incident_type' => ['required', 'string'],
            'severity' => ['required', 'string'],
            'patient_id' => ['nullable', 'uuid'],
            'encounter_id' => ['nullable', 'uuid'],
            'org_unit_id' => ['nullable', 'uuid'],
            'occurred_at' => ['required', 'date'],
            'description' => ['required', 'string'],
            'immediate_action' => ['nullable', 'string'],
            'reported_by' => ['nullable', 'uuid'],
            'is_anonymous' => ['nullable', 'boolean'],
            'reported_at' => ['nullable', 'date'],
            'status' => ['nullable', 'string'],
            'root_cause' => ['nullable', 'string'],
            'corrective_action' => ['nullable', 'string'],
            'closed_by' => ['nullable', 'uuid'],
            'closed_at' => ['nullable', 'date'],
        ];
    }
}
