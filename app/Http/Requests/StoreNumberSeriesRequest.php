<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `number_series`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreNumberSeriesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'series_code' => ['required', 'string'],
            'prefix' => ['nullable', 'string'],
            'suffix' => ['nullable', 'string'],
            'padding' => ['nullable', 'integer'],
            'next_value' => ['nullable', 'integer'],
            'resets_annually' => ['nullable', 'boolean'],
            'current_period' => ['nullable', 'string'],
        ];
    }
}
