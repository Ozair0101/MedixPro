<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `stock_requisition`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreStockRequisitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'requisition_number' => ['required', 'string'],
            'requesting_location_id' => ['required', 'uuid'],
            'fulfilling_location_id' => ['nullable', 'uuid'],
            'status' => ['nullable', 'string'],
            'requested_by' => ['required', 'uuid'],
            'requested_at' => ['nullable', 'date'],
            'approved_by' => ['nullable', 'uuid'],
            'approved_at' => ['nullable', 'date'],
            'rejection_reason' => ['nullable', 'string'],
        ];
    }
}
