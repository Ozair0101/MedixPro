<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `stock_count_line`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreStockCountLineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'stock_count_id' => ['required', 'uuid'],
            'stock_item_id' => ['required', 'uuid'],
            'stock_lot_id' => ['nullable', 'uuid'],
            'system_qty' => ['required', 'numeric'],
            'counted_qty' => ['required', 'numeric'],
            'variance_reason' => ['nullable', 'string'],
            'ledger_id' => ['nullable', 'integer'],
        ];
    }
}
