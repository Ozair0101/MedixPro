<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `transfusion_reaction`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreTransfusionReactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'transfusion_id' => ['required', 'uuid'],
            'reaction_type' => ['required', 'string'],
            'severity' => ['required', 'string'],
            'onset_at' => ['required', 'date'],
            'signs_symptoms' => ['required', 'string'],
            'action_taken' => ['nullable', 'string'],
            'reported_by' => ['required', 'uuid'],
            'investigated' => ['nullable', 'boolean'],
            'investigation_outcome' => ['nullable', 'string'],
        ];
    }
}
