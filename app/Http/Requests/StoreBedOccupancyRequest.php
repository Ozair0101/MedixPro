<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `bed_occupancy`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreBedOccupancyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bed_id' => ['required', 'uuid'],
            'admission_id' => ['required', 'uuid'],
            'patient_id' => ['required', 'uuid'],
            'occupied' => ['required', 'string'],
            'status' => ['nullable', 'string'],
            'cancelled' => ['nullable', 'boolean'],
            'ward_id' => ['required', 'uuid'],
            'ward_sex_policy' => ['required', 'string', 'max:1'],
            'patient_sex' => ['required', 'string', 'max:1'],
        ];
    }
}
