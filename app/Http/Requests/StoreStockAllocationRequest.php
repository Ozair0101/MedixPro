<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `stock_allocation`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreStockAllocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'stock_item_id' => ['required', 'uuid'],
            'location_id' => ['required', 'uuid'],
            'stock_lot_id' => ['nullable', 'uuid'],
            'qty' => ['required', 'numeric'],
            'reference_type' => ['required', 'string'],
            'reference_id' => ['required', 'uuid'],
            'status' => ['nullable', 'string'],
            'expires_at' => ['nullable', 'date'],
            'idempotency_key' => ['nullable', 'string'],
        ];
    }
}
