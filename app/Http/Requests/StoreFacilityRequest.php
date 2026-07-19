<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `facility`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreFacilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'moph_facility_code' => ['nullable', 'string'],
            'name_local' => ['required', 'string'],
            'name_latin' => ['required', 'string'],
            'facility_type' => ['required', 'string'],
            'moph_form_type' => ['nullable', 'string', 'max:2'],
            'province_pcode' => ['nullable', 'string', 'max:4'],
            'district_pcode' => ['nullable', 'string', 'max:6'],
            'address_detail' => ['nullable', 'string'],
            'phone' => ['nullable', 'string'],
            'licensed_beds' => ['nullable', 'integer'],
            'legal_name_local' => ['nullable', 'string'],
            'tin' => ['nullable', 'string', 'max:10'],
            'business_licence_no' => ['nullable', 'string'],
            'tax_exempt' => ['nullable', 'boolean'],
            'tax_exemption_ref' => ['nullable', 'string'],
            'tax_exemption_from' => ['nullable', 'date'],
            'tax_exemption_to' => ['nullable', 'date'],
            'electricity_source' => ['nullable', 'string'],
            'electricity_hours_per_day' => ['nullable', 'numeric'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
