<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `stock_lot`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreStockLotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'stock_item_id' => ['required', 'uuid'],
            'lot_number' => ['nullable', 'string'],
            'serial_number' => ['nullable', 'string'],
            'expiry_date' => ['nullable', 'date'],
            'removal_date' => ['nullable', 'date'],
            'manufacture_date' => ['nullable', 'date'],
            'gtin' => ['nullable', 'string', 'max:14'],
            'supplier_id' => ['nullable', 'uuid'],
            'lot_status' => ['nullable', 'string'],
        ];
    }
}
