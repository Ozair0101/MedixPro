<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `stock_location`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreStockLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'parent_id' => ['nullable', 'uuid'],
            'code' => ['required', 'string'],
            'name_local' => ['required', 'string'],
            'name_latin' => ['required', 'string'],
            'location_type' => ['required', 'string'],
            'counts_as_on_hand' => ['nullable', 'boolean'],
            'is_dispensing_point' => ['nullable', 'boolean'],
            'org_unit_id' => ['nullable', 'uuid'],
            'path' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
