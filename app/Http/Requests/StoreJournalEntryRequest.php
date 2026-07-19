<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `journal_entry`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreJournalEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'entry_number' => ['required', 'integer'],
            'period_id' => ['required', 'uuid'],
            'posting_date' => ['required', 'date'],
            'source_type' => ['required', 'string'],
            'source_id' => ['nullable', 'uuid'],
            'posting_rule_id' => ['nullable', 'uuid'],
            'description' => ['required', 'string'],
            'status' => ['nullable', 'string'],
            'reverses_id' => ['nullable', 'uuid'],
            'created_by' => ['required', 'uuid'],
        ];
    }
}
