<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `billable_item`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreBillableItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'item_code' => ['required', 'string'],
            'display_name_local' => ['required', 'string'],
            'display_name_latin' => ['required', 'string'],
            'technical_name' => ['nullable', 'string'],
            'category' => ['required', 'string'],
            'org_unit_id' => ['nullable', 'uuid'],
            'default_unit' => ['nullable', 'uuid'],
            'gl_revenue_account_id' => ['nullable', 'uuid'],
            'tax_category' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
