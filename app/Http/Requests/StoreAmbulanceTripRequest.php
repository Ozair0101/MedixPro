<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `ambulance_trip`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreAmbulanceTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ambulance_id' => ['required', 'uuid'],
            'patient_id' => ['nullable', 'uuid'],
            'trip_type' => ['required', 'string'],
            'origin' => ['required', 'string'],
            'destination' => ['required', 'string'],
            'dispatched_at' => ['required', 'date'],
            'arrived_at' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date'],
            'distance_km' => ['nullable', 'numeric'],
            'fuel_litres' => ['nullable', 'numeric'],
            'driver_id' => ['nullable', 'uuid'],
            'attendant_id' => ['nullable', 'uuid'],
            'charge_item_id' => ['nullable', 'uuid'],
        ];
    }
}
