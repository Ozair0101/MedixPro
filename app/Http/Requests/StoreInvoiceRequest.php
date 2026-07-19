<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `invoice`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_number' => ['required', 'string'],
            'account_id' => ['required', 'uuid'],
            'patient_id' => ['required', 'uuid'],
            'recipient_party_id' => ['required', 'uuid'],
            'status' => ['nullable', 'string'],
            'currency' => ['nullable', 'string', 'max:3'],
            'subtotal' => ['nullable', 'numeric'],
            'discount_total' => ['nullable', 'numeric'],
            'tax_total' => ['nullable', 'numeric'],
            'rounding_adjustment' => ['nullable', 'numeric'],
            'total_gross' => ['nullable', 'numeric'],
            'fx_rate_id' => ['nullable', 'uuid'],
            'issued_at' => ['nullable', 'date'],
            'issued_by' => ['nullable', 'uuid'],
            'due_date' => ['nullable', 'date'],
            'cancelled_reason' => ['nullable', 'string'],
        ];
    }
}
