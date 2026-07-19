<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `blood_donor`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreBloodDonorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'person_id' => ['nullable', 'uuid'],
            'donor_number' => ['required', 'string'],
            'blood_group' => ['required', 'string'],
            'is_permanently_deferred' => ['nullable', 'boolean'],
            'deferred_until' => ['nullable', 'date'],
            'deferral_reason' => ['nullable', 'string'],
            'last_donation_date' => ['nullable', 'date'],
        ];
    }
}
