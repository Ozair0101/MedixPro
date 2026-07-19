<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `sync_device`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreSyncDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'device_label' => ['required', 'string'],
            'assigned_user_id' => ['nullable', 'uuid'],
            'scope_org_unit_id' => ['nullable', 'uuid'],
            'scope_district_pcode' => ['nullable', 'string', 'max:6'],
            'last_sync_at' => ['nullable', 'date'],
            'last_seen_seq' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
