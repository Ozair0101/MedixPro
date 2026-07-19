<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `goods_receipt`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreGoodsReceiptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'grn_number' => ['required', 'string'],
            'purchase_order_id' => ['nullable', 'uuid'],
            'supplier_id' => ['required', 'uuid'],
            'location_id' => ['required', 'uuid'],
            'received_at' => ['nullable', 'date'],
            'received_by' => ['required', 'uuid'],
            'supplier_invoice_no' => ['nullable', 'string'],
            'supplier_invoice_date' => ['nullable', 'date'],
            'match_status' => ['nullable', 'string'],
            'discrepancy_notes' => ['nullable', 'string'],
        ];
    }
}
