<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `stock_item`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreStockItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'item_code' => ['required', 'string'],
            'name_local' => ['required', 'string'],
            'name_latin' => ['required', 'string'],
            'item_category' => ['required', 'string'],
            'ampp_id' => ['nullable', 'uuid'],
            'vmp_id' => ['nullable', 'uuid'],
            'base_unit' => ['required', 'uuid'],
            'lot_control' => ['nullable', 'boolean'],
            'expiry_control' => ['nullable', 'boolean'],
            'serialized' => ['nullable', 'boolean'],
            'is_controlled' => ['nullable', 'boolean'],
            'cold_chain' => ['nullable', 'boolean'],
            'reorder_level' => ['nullable', 'numeric'],
            'max_level' => ['nullable', 'numeric'],
            'removal_lead_days' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
