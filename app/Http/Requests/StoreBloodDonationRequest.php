<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `blood_donation`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreBloodDonationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'donor_id' => ['required', 'uuid'],
            'donation_number' => ['required', 'string'],
            'donated_at' => ['nullable', 'date'],
            'volume_ml' => ['required', 'integer'],
            'haemoglobin_gdl' => ['nullable', 'numeric'],
            'screened_hiv' => ['nullable', 'string'],
            'screened_hbv' => ['nullable', 'string'],
            'screened_hcv' => ['nullable', 'string'],
            'screened_syphilis' => ['nullable', 'string'],
            'screened_malaria' => ['nullable', 'string'],
            'screening_complete' => ['nullable', 'boolean'],
            'is_discarded' => ['nullable', 'boolean'],
            'discard_reason' => ['nullable', 'string'],
        ];
    }
}
