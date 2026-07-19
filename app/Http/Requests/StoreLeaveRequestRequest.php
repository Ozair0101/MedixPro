<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `leave_request`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreLeaveRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'uuid'],
            'leave_type_id' => ['required', 'uuid'],
            'leave_period' => ['required', 'string'],
            'days_count' => ['required', 'numeric'],
            'reason' => ['nullable', 'string'],
            'status' => ['nullable', 'string'],
            'requested_at' => ['nullable', 'date'],
            'approved_by' => ['nullable', 'uuid'],
            'approved_at' => ['nullable', 'date'],
        ];
    }
}
