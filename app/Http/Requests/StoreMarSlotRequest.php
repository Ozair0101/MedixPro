<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `mar_slot`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreMarSlotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'drug_order_id' => ['required', 'uuid'],
            'patient_id' => ['required', 'uuid'],
            'admission_id' => ['nullable', 'uuid'],
            'scheduled_at' => ['nullable', 'date'],
            'window_start' => ['nullable', 'date'],
            'window_end' => ['nullable', 'date'],
            'is_prn' => ['nullable', 'boolean'],
            'slot_status' => ['nullable', 'string'],
            'hold_reason' => ['nullable', 'string'],
            'administration_id' => ['nullable', 'uuid'],
        ];
    }
}
