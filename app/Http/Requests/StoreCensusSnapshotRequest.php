<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `census_snapshot`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreCensusSnapshotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'census_date' => ['required', 'date'],
            'ward_id' => ['required', 'uuid'],
            'occupied_beds' => ['required', 'integer'],
            'available_beds' => ['required', 'integer'],
            'admissions' => ['nullable', 'integer'],
            'discharges' => ['nullable', 'integer'],
            'deaths' => ['nullable', 'integer'],
            'transfers_in' => ['nullable', 'integer'],
            'transfers_out' => ['nullable', 'integer'],
            'computed_at' => ['nullable', 'date'],
        ];
    }
}
