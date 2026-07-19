<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `stock_requisition_line`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreStockRequisitionLineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'requisition_id' => ['required', 'uuid'],
            'stock_item_id' => ['required', 'uuid'],
            'qty_requested' => ['required', 'numeric'],
            'qty_approved' => ['nullable', 'numeric'],
            'qty_fulfilled' => ['nullable', 'numeric'],
            'unit_id' => ['required', 'uuid'],
        ];
    }
}
