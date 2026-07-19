<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `mortuary_record`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreMortuaryRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['nullable', 'uuid'],
            'deceased_name' => ['nullable', 'string'],
            'received_at' => ['nullable', 'date'],
            'storage_unit' => ['nullable', 'string'],
            'death_time' => ['nullable', 'date'],
            'cause_of_death' => ['nullable', 'string'],
            'is_medico_legal' => ['nullable', 'boolean'],
            'released_at' => ['nullable', 'date'],
            'released_to_name' => ['nullable', 'string'],
            'released_to_relation' => ['nullable', 'string'],
            'released_to_id_number' => ['nullable', 'string'],
            'authorized_by' => ['nullable', 'uuid'],
            'death_certificate_no' => ['nullable', 'string'],
        ];
    }
}
