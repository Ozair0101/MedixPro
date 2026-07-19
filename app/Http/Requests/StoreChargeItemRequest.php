<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `charge_item`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreChargeItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'uuid'],
            'encounter_id' => ['nullable', 'uuid'],
            'account_id' => ['nullable', 'uuid'],
            'source_type' => ['required', 'string'],
            'source_id' => ['nullable', 'uuid'],
            'item_id' => ['required', 'uuid'],
            'quantity' => ['required', 'numeric'],
            'unit_id' => ['nullable', 'uuid'],
            'tariff_id' => ['nullable', 'uuid'],
            'tariff_price_id' => ['nullable', 'uuid'],
            'unit_price' => ['required', 'numeric'],
            'gross_amount' => ['required', 'numeric'],
            'override_amount' => ['nullable', 'numeric'],
            'override_reason' => ['nullable', 'string'],
            'status' => ['nullable', 'string'],
            'service_date' => ['required', 'date'],
            'performer_id' => ['nullable', 'uuid'],
            'cost_centre_id' => ['nullable', 'uuid'],
            'parent_id' => ['nullable', 'uuid'],
            'entered_by' => ['required', 'uuid'],
            'entered_at' => ['nullable', 'date'],
        ];
    }
}
