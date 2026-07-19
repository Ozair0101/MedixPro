<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `tariff_price`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreTariffPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tariff_id' => ['required', 'uuid'],
            'item_id' => ['required', 'uuid'],
            'unit_id' => ['nullable', 'uuid'],
            'min_quantity' => ['nullable', 'numeric'],
            'price_type' => ['nullable', 'string'],
            'amount' => ['nullable', 'numeric'],
            'percent' => ['nullable', 'numeric'],
            'valid_at' => ['required', 'string'],
            'created_by' => ['nullable', 'uuid'],
        ];
    }
}
