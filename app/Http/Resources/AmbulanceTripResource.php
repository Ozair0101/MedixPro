<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `ambulance_trip`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class AmbulanceTripResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ambulance_id' => $this->ambulance_id,
            'patient_id' => $this->patient_id,
            'trip_type' => $this->trip_type,
            'origin' => $this->origin,
            'destination' => $this->destination,
            'dispatched_at' => $this->dispatched_at?->toIso8601String(),
            'arrived_at' => $this->arrived_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'distance_km' => $this->distance_km,
            'fuel_litres' => $this->fuel_litres,
            'driver_id' => $this->driver_id,
            'attendant_id' => $this->attendant_id,
            'charge_item_id' => $this->charge_item_id,
        ];
    }
}
