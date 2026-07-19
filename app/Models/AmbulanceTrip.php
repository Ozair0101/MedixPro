<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `ambulance_trip` table.
 */
class AmbulanceTrip extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'ambulance_trip';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'ambulance_id', 'patient_id', 'trip_type',
        'origin', 'destination', 'dispatched_at', 'arrived_at',
        'completed_at', 'distance_km', 'fuel_litres', 'driver_id',
        'attendant_id', 'charge_item_id',
    ];

    protected $casts = [
        'dispatched_at' => 'datetime',
        'arrived_at' => 'datetime',
        'completed_at' => 'datetime',
        'distance_km' => 'float',
        'fuel_litres' => 'float',
    ];
}
