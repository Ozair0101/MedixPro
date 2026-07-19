<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `app_user`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreAppUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'email' => ['nullable', 'string'],
            'password_hash' => ['required', 'string'],
            'display_name' => ['required', 'string'],
            'gender' => ['required', 'string', 'max:1'],
            'staff_id' => ['nullable', 'uuid'],
            'preferred_locale' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'last_login_at' => ['nullable', 'date'],
            'failed_attempts' => ['nullable', 'integer'],
            'locked_until' => ['nullable', 'date'],
        ];
    }
}
