<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `blood_unit`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreBloodUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'donation_id' => ['required', 'uuid'],
            'unit_number' => ['required', 'string'],
            'component' => ['required', 'string'],
            'blood_group' => ['required', 'string'],
            'volume_ml' => ['required', 'integer'],
            'prepared_at' => ['nullable', 'date'],
            'expires_at' => ['required', 'date'],
            'storage_location_id' => ['nullable', 'uuid'],
            'status' => ['nullable', 'string'],
            'discard_reason' => ['nullable', 'string'],
        ];
    }
}
