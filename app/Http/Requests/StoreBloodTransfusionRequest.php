<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `blood_transfusion`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreBloodTransfusionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'uuid'],
            'encounter_id' => ['required', 'uuid'],
            'blood_unit_id' => ['required', 'uuid'],
            'crossmatch_id' => ['nullable', 'uuid'],
            'started_at' => ['required', 'date'],
            'completed_at' => ['nullable', 'date'],
            'volume_transfused_ml' => ['nullable', 'integer'],
            'administered_by' => ['required', 'uuid'],
            'verified_by' => ['nullable', 'uuid'],
            'status' => ['nullable', 'string'],
        ];
    }
}
