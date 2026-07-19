<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `notification`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'channel' => ['required', 'string'],
            'recipient_user_id' => ['nullable', 'uuid'],
            'recipient_phone' => ['nullable', 'string'],
            'template_code' => ['required', 'string'],
            'body' => ['required', 'string'],
            'consent_verified' => ['nullable', 'boolean'],
            'status' => ['nullable', 'string'],
            'queued_at' => ['nullable', 'date'],
            'sent_at' => ['nullable', 'date'],
            'error' => ['nullable', 'string'],
        ];
    }
}
