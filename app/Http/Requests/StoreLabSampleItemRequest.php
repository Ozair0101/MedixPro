<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `lab_sample_item`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreLabSampleItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sample_id' => ['required', 'uuid'],
            'parent_item_id' => ['nullable', 'uuid'],
            'specimen_type_concept_id' => ['required', 'uuid'],
            'container_type' => ['nullable', 'string'],
            'quantity' => ['nullable', 'numeric'],
            'quantity_unit' => ['nullable', 'string'],
            'collected_at' => ['nullable', 'date'],
            'collected_by' => ['nullable', 'uuid'],
            'collection_method' => ['nullable', 'string'],
            'collection_conditions' => ['nullable', 'string'],
            'temperature_c' => ['nullable', 'numeric'],
            'received_at' => ['nullable', 'date'],
            'rejected' => ['nullable', 'boolean'],
            'rejection_reason' => ['nullable', 'string'],
            'barcode' => ['nullable', 'string'],
        ];
    }
}
