<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `shamsi_month`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreShamsiMonthRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shamsi_year' => ['required', 'integer'],
            'month_no' => ['required', 'integer'],
            'name_dari' => ['required', 'string'],
            'name_latin' => ['required', 'string'],
            'name_pashto' => ['nullable', 'string'],
            'gregorian_period' => ['required', 'string'],
            'reporting_gregorian_month' => ['required', 'date'],
            'fiscal_year_id' => ['required', 'uuid'],
        ];
    }
}
