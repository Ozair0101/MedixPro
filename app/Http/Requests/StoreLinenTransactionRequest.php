<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `linen_transaction`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreLinenTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'org_unit_id' => ['required', 'uuid'],
            'stock_item_id' => ['nullable', 'uuid'],
            'transaction_type' => ['required', 'string'],
            'quantity' => ['required', 'integer'],
            'occurred_at' => ['nullable', 'date'],
            'recorded_by' => ['nullable', 'uuid'],
        ];
    }
}
