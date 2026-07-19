<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `moph_indicator`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreMophIndicatorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string'],
            'name_local' => ['required', 'string'],
            'name_latin' => ['required', 'string'],
            'domain' => ['required', 'string'],
            'definition' => ['required', 'string'],
            'numerator_spec' => ['nullable', 'string'],
            'denominator_spec' => ['nullable', 'string'],
            'data_source' => ['required', 'string'],
            'frequency' => ['required', 'string'],
            'is_computable_here' => ['nullable', 'boolean'],
        ];
    }
}
