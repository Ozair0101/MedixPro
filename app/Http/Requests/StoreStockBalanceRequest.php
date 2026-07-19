<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `stock_balance`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreStockBalanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'location_id' => ['required', 'uuid'],
            'stock_lot_id' => ['required', 'uuid'],
            'qty_on_hand' => ['nullable', 'numeric'],
            'qty_allocated' => ['nullable', 'numeric'],
            'moving_avg_cost' => ['nullable', 'numeric'],
            'version' => ['nullable', 'integer'],
        ];
    }
}
