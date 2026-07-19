<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `clinical_order`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreClinicalOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_number' => ['required', 'string'],
            'requisition_number' => ['nullable', 'string'],
            'patient_id' => ['required', 'uuid'],
            'encounter_id' => ['required', 'uuid'],
            'order_type_code' => ['required', 'string'],
            'concept_id' => ['required', 'uuid'],
            'status' => ['nullable', 'string'],
            'intent' => ['nullable', 'string'],
            'priority' => ['nullable', 'string'],
            'order_action' => ['nullable', 'string'],
            'previous_order_id' => ['nullable', 'uuid'],
            'ordered_by' => ['required', 'uuid'],
            'ordered_at' => ['nullable', 'date'],
            'is_verbal_order' => ['nullable', 'boolean'],
            'countersigned_by' => ['nullable', 'uuid'],
            'countersigned_at' => ['nullable', 'date'],
            'scheduled_at' => ['nullable', 'date'],
            'instructions' => ['nullable', 'string'],
            'order_reason' => ['nullable', 'string'],
            'stopped_at' => ['nullable', 'date'],
            'fulfiller_status' => ['nullable', 'string'],
        ];
    }
}
