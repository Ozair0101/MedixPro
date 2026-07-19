<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `cashier_shift`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreCashierShiftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cash_point_id' => ['required', 'uuid'],
            'cashier_id' => ['required', 'uuid'],
            'opened_at' => ['nullable', 'date'],
            'opening_float' => ['nullable', 'numeric'],
            'closed_at' => ['nullable', 'date'],
            'declared_cash' => ['nullable', 'numeric'],
            'expected_cash' => ['nullable', 'numeric'],
            'variance_reason' => ['nullable', 'string'],
            'closed_by' => ['nullable', 'uuid'],
            'status' => ['nullable', 'string'],
        ];
    }
}
