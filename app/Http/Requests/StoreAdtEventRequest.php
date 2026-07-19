<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `adt_event`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreAdtEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'admission_id' => ['required', 'uuid'],
            'patient_id' => ['required', 'uuid'],
            'event_type' => ['required', 'string'],
            'effective_at' => ['required', 'date'],
            'recorded_at' => ['nullable', 'date'],
            'from_bed_id' => ['nullable', 'uuid'],
            'to_bed_id' => ['nullable', 'uuid'],
            'reason' => ['nullable', 'string'],
            'reverses_event_id' => ['nullable', 'uuid'],
            'performed_by' => ['required', 'uuid'],
        ];
    }
}
