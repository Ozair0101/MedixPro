<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `lab_critical_notification`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreLabCriticalNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'result_id' => ['required', 'uuid'],
            'notified_practitioner_id' => ['nullable', 'uuid'],
            'notified_name' => ['nullable', 'string'],
            'notified_at' => ['nullable', 'date'],
            'notified_by' => ['required', 'uuid'],
            'method' => ['required', 'string'],
            'read_back_confirmed' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
