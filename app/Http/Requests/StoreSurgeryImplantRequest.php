<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `surgery_implant`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreSurgeryImplantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'surgery_id' => ['required', 'uuid'],
            'stock_item_id' => ['nullable', 'uuid'],
            'stock_lot_id' => ['nullable', 'uuid'],
            'serial_number' => ['nullable', 'string'],
            'description' => ['required', 'string'],
            'quantity' => ['nullable', 'integer'],
        ];
    }
}
