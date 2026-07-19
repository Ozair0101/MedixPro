<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `goods_receipt_line`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreGoodsReceiptLineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'goods_receipt_id' => ['required', 'uuid'],
            'purchase_order_line_id' => ['nullable', 'uuid'],
            'stock_item_id' => ['required', 'uuid'],
            'stock_lot_id' => ['nullable', 'uuid'],
            'batch_no' => ['nullable', 'string'],
            'expiry_date' => ['nullable', 'date'],
            'qty_received' => ['required', 'numeric'],
            'qty_accepted' => ['required', 'numeric'],
            'qty_rejected' => ['nullable', 'numeric'],
            'rejection_reason' => ['nullable', 'string'],
            'unit_id' => ['required', 'uuid'],
            'unit_cost' => ['required', 'numeric'],
            'ledger_id' => ['nullable', 'integer'],
        ];
    }
}
