<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `asset`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'asset_tag' => ['required', 'string'],
            'name' => ['required', 'string'],
            'category' => ['required', 'string'],
            'manufacturer' => ['nullable', 'string'],
            'model' => ['nullable', 'string'],
            'serial_number' => ['nullable', 'string'],
            'location_id' => ['nullable', 'uuid'],
            'org_unit_id' => ['nullable', 'uuid'],
            'purchase_date' => ['nullable', 'date'],
            'purchase_cost' => ['nullable', 'numeric'],
            'warranty_expires' => ['nullable', 'date'],
            'status' => ['nullable', 'string'],
            'is_donated' => ['nullable', 'boolean'],
            'donor_name' => ['nullable', 'string'],
        ];
    }
}
