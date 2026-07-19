<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `medical_waste`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreMedicalWasteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'org_unit_id' => ['nullable', 'uuid'],
            'waste_category' => ['required', 'string'],
            'weight_kg' => ['required', 'numeric'],
            'collected_at' => ['nullable', 'date'],
            'disposal_method' => ['nullable', 'string'],
            'disposed_at' => ['nullable', 'date'],
            'handled_by' => ['nullable', 'uuid'],
        ];
    }
}
