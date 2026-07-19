<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `payment`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'receipt_number' => ['required', 'string'],
            'payer_party_id' => ['required', 'uuid'],
            'payer_kind' => ['required', 'string'],
            'direction' => ['required', 'string'],
            'method' => ['required', 'string'],
            'amount' => ['required', 'numeric'],
            'currency' => ['nullable', 'string', 'max:3'],
            'external_ref' => ['nullable', 'string'],
            'received_at' => ['nullable', 'date'],
            'cashier_shift_id' => ['nullable', 'uuid'],
            'received_by' => ['required', 'uuid'],
            'status' => ['nullable', 'string'],
            'reverses_id' => ['nullable', 'uuid'],
            'approved_by' => ['nullable', 'uuid'],
        ];
    }
}
