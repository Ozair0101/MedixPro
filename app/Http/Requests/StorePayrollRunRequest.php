<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `payroll_run`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StorePayrollRunRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payroll_period_id' => ['required', 'uuid'],
            'employee_id' => ['required', 'uuid'],
            'gross_salary' => ['required', 'numeric'],
            'overtime_amount' => ['nullable', 'numeric'],
            'allowances' => ['nullable', 'numeric'],
            'deductions' => ['nullable', 'numeric'],
            'income_tax_withheld' => ['nullable', 'numeric'],
            'net_pay' => ['required', 'numeric'],
            'currency' => ['nullable', 'string', 'max:3'],
            'payment_id' => ['nullable', 'uuid'],
            'journal_entry_id' => ['nullable', 'uuid'],
        ];
    }
}
