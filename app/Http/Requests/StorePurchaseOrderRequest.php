<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `purchase_order`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StorePurchaseOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'po_number' => ['required', 'string'],
            'supplier_id' => ['required', 'uuid'],
            'requisition_id' => ['nullable', 'uuid'],
            'status' => ['nullable', 'string'],
            'order_date' => ['nullable', 'date'],
            'expected_date' => ['nullable', 'date'],
            'currency' => ['nullable', 'string', 'max:3'],
            'subtotal' => ['nullable', 'numeric'],
            'tax_amount' => ['nullable', 'numeric'],
            'total_amount' => ['nullable', 'numeric'],
            'is_donation' => ['nullable', 'boolean'],
            'donor_name' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'approved_by' => ['nullable', 'uuid'],
            'approved_at' => ['nullable', 'date'],
        ];
    }
}
