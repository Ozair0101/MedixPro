<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `clinic_schedule`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreClinicScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'org_unit_id' => ['required', 'uuid'],
            'practitioner_id' => ['nullable', 'uuid'],
            'weekday' => ['required', 'integer'],
            'start_time' => ['required', 'string'],
            'end_time' => ['required', 'string'],
            'slot_minutes' => ['nullable', 'integer'],
            'max_walk_ins' => ['nullable', 'integer'],
            'valid_period' => ['nullable', 'string'],
        ];
    }
}
