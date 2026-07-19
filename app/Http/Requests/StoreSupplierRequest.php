<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `supplier`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string'],
            'name' => ['required', 'string'],
            'contact_person' => ['nullable', 'string'],
            'phone' => ['nullable', 'string'],
            'email' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'tin' => ['nullable', 'string', 'max:10'],
            'payment_terms' => ['nullable', 'string'],
            'has_business_licence' => ['nullable', 'boolean'],
            'withholding_rate' => ['nullable', 'numeric'],
            'rating' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
