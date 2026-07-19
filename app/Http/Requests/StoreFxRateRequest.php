<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `fx_rate`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreFxRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'base_currency' => ['nullable', 'string', 'max:3'],
            'quote_currency' => ['required', 'string', 'max:3'],
            'rate_date' => ['required', 'date'],
            'cash_buy' => ['nullable', 'numeric'],
            'cash_sell' => ['nullable', 'numeric'],
            'transfer_buy' => ['nullable', 'numeric'],
            'transfer_sell' => ['nullable', 'numeric'],
            'source' => ['required', 'string'],
            'raw_snapshot' => ['nullable', 'string'],
            'recorded_at' => ['nullable', 'date'],
            'recorded_by' => ['nullable', 'uuid'],
        ];
    }
}
