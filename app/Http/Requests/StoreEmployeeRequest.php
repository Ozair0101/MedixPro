<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `employee`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'person_id' => ['nullable', 'uuid'],
            'practitioner_id' => ['nullable', 'uuid'],
            'employee_number' => ['required', 'string'],
            'moph_staff_code' => ['nullable', 'string'],
            'name_local' => ['required', 'string'],
            'name_latin' => ['required', 'string'],
            'gender' => ['required', 'string', 'max:1'],
            'org_unit_id' => ['nullable', 'uuid'],
            'job_grade_id' => ['nullable', 'uuid'],
            'job_title' => ['required', 'string'],
            'staff_category' => ['required', 'string'],
            'employment_type' => ['required', 'string'],
            'hired_on' => ['required', 'date'],
            'terminated_on' => ['nullable', 'date'],
            'termination_reason' => ['nullable', 'string'],
            'phone' => ['nullable', 'string'],
            'bank_account' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
