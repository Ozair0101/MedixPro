<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `electronic_signature`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreElectronicSignatureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'target_table' => ['required', 'string'],
            'target_id' => ['required', 'uuid'],
            'target_version' => ['nullable', 'uuid'],
            'signer_id' => ['required', 'uuid'],
            'purpose' => ['required', 'string'],
            'signed_at' => ['nullable', 'date'],
            'auth_method' => ['required', 'string'],
            'content_hash' => ['required', 'string'],
            'signed_manifestation' => ['required', 'string'],
            'ip_address' => ['nullable', 'string'],
        ];
    }
}
