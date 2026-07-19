<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `attachment`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreAttachmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'owner_type' => ['required', 'string'],
            'owner_id' => ['required', 'uuid'],
            'file_name' => ['required', 'string'],
            'mime_type' => ['required', 'string'],
            'byte_size' => ['required', 'integer'],
            'storage_path' => ['required', 'string'],
            'sha256' => ['required', 'string', 'max:64'],
            'is_sensitive' => ['nullable', 'boolean'],
            'uploaded_by' => ['required', 'uuid'],
            'uploaded_at' => ['nullable', 'date'],
        ];
    }
}
