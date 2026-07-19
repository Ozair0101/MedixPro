<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `dispense_item`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreDispenseItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'dispense_id' => ['required', 'uuid'],
            'stock_item_id' => ['required', 'uuid'],
            'stock_lot_id' => ['nullable', 'uuid'],
            'quantity' => ['required', 'numeric'],
            'unit_id' => ['required', 'uuid'],
            'base_quantity' => ['required', 'numeric'],
            'unit_price' => ['nullable', 'numeric'],
            'ledger_id' => ['nullable', 'integer'],
        ];
    }
}
