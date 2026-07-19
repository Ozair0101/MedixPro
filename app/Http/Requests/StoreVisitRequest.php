<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `visit`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreVisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'uuid'],
            'visit_number' => ['required', 'string'],
            'visit_type' => ['required', 'string'],
            'period' => ['required', 'string'],
            'admission_id' => ['nullable', 'uuid'],
            'referral_source' => ['nullable', 'string'],
            'referred_from_facility' => ['nullable', 'string'],
        ];
    }
}
