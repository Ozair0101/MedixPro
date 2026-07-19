<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `credit_note`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreCreditNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'credit_note_number' => ['required', 'string'],
            'invoice_id' => ['required', 'uuid'],
            'reason_code' => ['required', 'string'],
            'total_amount' => ['required', 'numeric'],
            'issued_at' => ['nullable', 'date'],
            'issued_by' => ['required', 'uuid'],
            'approved_by' => ['nullable', 'uuid'],
        ];
    }
}
