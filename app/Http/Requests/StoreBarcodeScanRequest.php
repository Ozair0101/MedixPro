<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for `barcode_scan`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreBarcodeScanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'raw_data' => ['required', 'string'],
            'symbology' => ['nullable', 'string'],
            'ai_01_gtin' => ['nullable', 'string', 'max:14'],
            'ai_10_lot' => ['nullable', 'string'],
            'ai_17_expiry' => ['nullable', 'date'],
            'ai_21_serial' => ['nullable', 'string'],
            'resolved_lot_id' => ['nullable', 'uuid'],
            'scanned_at' => ['nullable', 'date'],
            'scanned_by' => ['nullable', 'uuid'],
        ];
    }
}
