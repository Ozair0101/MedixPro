<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `virtual_product_ingredient`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreVirtualProductIngredientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'substance_id' => ['required', 'uuid'],
            'strength_num_value' => ['nullable', 'numeric'],
            'strength_num_unit' => ['nullable', 'uuid'],
            'strength_den_value' => ['nullable', 'numeric'],
            'strength_den_unit' => ['nullable', 'uuid'],
        ];
    }
}
