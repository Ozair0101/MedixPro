<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `appointment` table.
 */
class Appointment extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'appointment';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'patient_id', 'org_unit_id', 'practitioner_id',
        'slot', 'status', 'reason', 'booked_at',
        'booked_by', 'cancelled_reason', 'rescheduled_to_id', 'encounter_id',
    ];

    protected $casts = [
        'booked_at' => 'datetime',
    ];
}
