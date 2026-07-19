<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `dews_notification`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreDewsNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'uuid'],
            'encounter_id' => ['nullable', 'uuid'],
            'priority_condition_id' => ['required', 'uuid'],
            'case_classification' => ['nullable', 'string'],
            'onset_date' => ['nullable', 'date'],
            'detected_at' => ['nullable', 'date'],
            'reported_at' => ['nullable', 'date'],
            'reported_by' => ['nullable', 'uuid'],
            'channel' => ['nullable', 'string'],
            'outcome' => ['nullable', 'string'],
            'status' => ['nullable', 'string'],
        ];
    }
}
