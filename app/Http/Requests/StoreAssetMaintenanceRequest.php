<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `asset_maintenance`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreAssetMaintenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'asset_id' => ['required', 'uuid'],
            'maintenance_type' => ['required', 'string'],
            'scheduled_date' => ['nullable', 'date'],
            'performed_date' => ['nullable', 'date'],
            'performed_by' => ['nullable', 'string'],
            'cost' => ['nullable', 'numeric'],
            'downtime_hours' => ['nullable', 'numeric'],
            'outcome' => ['nullable', 'string'],
            'next_due_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string'],
        ];
    }
}
