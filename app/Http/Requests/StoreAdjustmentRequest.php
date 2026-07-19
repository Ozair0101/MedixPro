<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `adjustment`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'account_id' => ['required', 'uuid'],
            'invoice_line_id' => ['nullable', 'uuid'],
            'kind' => ['required', 'string'],
            'reason_code' => ['required', 'string'],
            'amount' => ['required', 'numeric'],
            'posted_at' => ['nullable', 'date'],
            'posted_by' => ['required', 'uuid'],
            'approved_by' => ['nullable', 'uuid'],
            'reverses_id' => ['nullable', 'uuid'],
        ];
    }
}
