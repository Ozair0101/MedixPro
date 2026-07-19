<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `party`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StorePartyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'party_type' => ['required', 'string'],
            'person_id' => ['nullable', 'uuid'],
            'organization_name' => ['nullable', 'string'],
            'tin' => ['nullable', 'string', 'max:10'],
            'phone' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
        ];
    }
}
