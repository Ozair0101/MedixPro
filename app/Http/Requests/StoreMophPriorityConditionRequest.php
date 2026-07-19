<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `moph_priority_condition`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreMophPriorityConditionRequest extends FormRequest
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
            'form' => ['required', 'string'],
            'form_section' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer'],
            'new_case_interval_days' => ['nullable', 'integer'],
            'uses_family_planning_rules' => ['nullable', 'boolean'],
            'is_notifiable' => ['nullable', 'boolean'],
            'valid_period' => ['nullable', 'string'],
        ];
    }
}
