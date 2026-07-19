<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `stock_transfer`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreStockTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'transfer_number' => ['required', 'string'],
            'requisition_id' => ['nullable', 'uuid'],
            'source_location_id' => ['required', 'uuid'],
            'transit_location_id' => ['nullable', 'uuid'],
            'dest_location_id' => ['required', 'uuid'],
            'status' => ['nullable', 'string'],
            'dispatched_at' => ['nullable', 'date'],
            'dispatched_by' => ['nullable', 'uuid'],
            'received_at' => ['nullable', 'date'],
            'received_by' => ['nullable', 'uuid'],
        ];
    }
}
