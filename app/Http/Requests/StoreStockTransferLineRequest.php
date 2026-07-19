<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `stock_transfer_line`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreStockTransferLineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'transfer_id' => ['required', 'uuid'],
            'stock_item_id' => ['required', 'uuid'],
            'stock_lot_id' => ['nullable', 'uuid'],
            'qty_dispatched' => ['nullable', 'numeric'],
            'qty_received' => ['nullable', 'numeric'],
            'unit_id' => ['required', 'uuid'],
        ];
    }
}
