<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `lab_test`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreLabTestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string'],
            'concept_id' => ['required', 'uuid'],
            'section_id' => ['required', 'uuid'],
            'name_local' => ['required', 'string'],
            'name_latin' => ['required', 'string'],
            'loinc_code' => ['nullable', 'string'],
            'specimen_type_concept_id' => ['nullable', 'uuid'],
            'container_type' => ['nullable', 'string'],
            'minimum_volume_ml' => ['nullable', 'numeric'],
            'result_datatype' => ['required', 'string'],
            'unit_concept_id' => ['nullable', 'uuid'],
            'significant_digits' => ['nullable', 'integer'],
            'tat_average_minutes' => ['nullable', 'integer'],
            'tat_warning_minutes' => ['nullable', 'integer'],
            'tat_max_minutes' => ['nullable', 'integer'],
            'is_orderable' => ['nullable', 'boolean'],
            'is_reportable' => ['nullable', 'boolean'],
            'requires_fasting' => ['nullable', 'boolean'],
            'billable_item_id' => ['nullable', 'uuid'],
            'valid_period' => ['nullable', 'string'],
        ];
    }
}
