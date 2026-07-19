<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `employment_contract`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreEmploymentContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'uuid'],
            'contract_number' => ['nullable', 'string'],
            'gross_salary' => ['required', 'numeric'],
            'currency' => ['nullable', 'string', 'max:3'],
            'contract_period' => ['required', 'string'],
            'funding_source' => ['nullable', 'string'],
        ];
    }
}
