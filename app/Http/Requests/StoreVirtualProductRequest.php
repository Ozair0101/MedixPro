<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `virtual_product`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreVirtualProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vtm_id' => ['nullable', 'uuid'],
            'name_latin' => ['required', 'string'],
            'name_local' => ['nullable', 'string'],
            'dose_form_id' => ['nullable', 'uuid'],
            'is_combination' => ['nullable', 'boolean'],
            'prescribable' => ['nullable', 'boolean'],
            'is_controlled' => ['nullable', 'boolean'],
            'controlled_schedule' => ['nullable', 'string'],
            'is_essential_medicine' => ['nullable', 'boolean'],
            'is_high_alert' => ['nullable', 'boolean'],
            'retired' => ['nullable', 'boolean'],
        ];
    }
}
