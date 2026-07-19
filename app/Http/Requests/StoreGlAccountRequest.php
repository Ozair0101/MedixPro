<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `gl_account`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreGlAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string'],
            'name_local' => ['required', 'string'],
            'name_latin' => ['required', 'string'],
            'account_type' => ['required', 'string'],
            'normal_side' => ['required', 'string', 'max:2'],
            'parent_id' => ['nullable', 'uuid'],
            'is_postable' => ['nullable', 'boolean'],
            'currency' => ['nullable', 'string', 'max:3'],
            'must_not_go_negative' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
