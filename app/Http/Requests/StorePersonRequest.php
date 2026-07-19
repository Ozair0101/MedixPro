<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `person`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StorePersonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name_local' => ['required', 'string'],
            'name_latin' => ['nullable', 'string'],
            'given_name' => ['required', 'string'],
            'father_name' => ['nullable', 'string'],
            'grandfather_name' => ['nullable', 'string'],
            'family_or_tribal_name' => ['nullable', 'string'],
            'honorifics' => ['nullable', 'string'],
            'name_search' => ['nullable', 'string'],
            'name_soundex' => ['nullable', 'string'],
            'gender' => ['required', 'string', 'max:1'],
            'birth_date' => ['nullable', 'date'],
            'birth_date_precision' => ['nullable', 'string'],
            'birth_date_estimated' => ['nullable', 'boolean'],
            'approximate_age_years' => ['nullable', 'integer'],
            'marital_status' => ['nullable', 'string'],
            'is_deceased' => ['nullable', 'boolean'],
            'deceased_at' => ['nullable', 'date'],
            'cause_of_death' => ['nullable', 'string'],
            'province_pcode' => ['nullable', 'string', 'max:4'],
            'district_pcode' => ['nullable', 'string', 'max:6'],
            'village' => ['nullable', 'string'],
            'address_detail' => ['nullable', 'string'],
            'created_by' => ['nullable', 'uuid'],
            'updated_by' => ['nullable', 'uuid'],
            'voided' => ['nullable', 'boolean'],
            'voided_by' => ['nullable', 'uuid'],
            'voided_at' => ['nullable', 'date'],
            'void_reason' => ['nullable', 'string'],
        ];
    }
}
