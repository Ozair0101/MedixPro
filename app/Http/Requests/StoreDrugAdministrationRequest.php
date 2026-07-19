<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `drug_administration`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreDrugAdministrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mar_slot_id' => ['nullable', 'uuid'],
            'drug_order_id' => ['required', 'uuid'],
            'patient_id' => ['required', 'uuid'],
            'dispense_id' => ['nullable', 'uuid'],
            'stock_lot_id' => ['nullable', 'uuid'],
            'dose_given' => ['required', 'numeric'],
            'dose_unit_concept_id' => ['nullable', 'uuid'],
            'route_concept_id' => ['nullable', 'uuid'],
            'site' => ['nullable', 'string'],
            'administered_at' => ['required', 'date'],
            'administered_by' => ['required', 'uuid'],
            'witnessed_by' => ['nullable', 'uuid'],
            'status' => ['nullable', 'string'],
            'not_done_reason' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
