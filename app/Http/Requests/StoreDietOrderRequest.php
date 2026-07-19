<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `diet_order`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreDietOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'uuid'],
            'admission_id' => ['nullable', 'uuid'],
            'order_id' => ['nullable', 'uuid'],
            'diet_type' => ['required', 'string'],
            'special_instructions' => ['nullable', 'string'],
            'valid_period' => ['required', 'string'],
            'status' => ['nullable', 'string'],
        ];
    }
}
