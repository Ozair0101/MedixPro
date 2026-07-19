<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `geo_district`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreGeoDistrictRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'province_pcode' => ['required', 'string', 'max:4'],
            'name_latin' => ['required', 'string'],
            'name_dari' => ['required', 'string'],
            'name_pashto' => ['nullable', 'string'],
            'adm2_type' => ['nullable', 'string'],
            'valid_on' => ['required', 'date'],
            'valid_to' => ['nullable', 'date'],
        ];
    }
}
