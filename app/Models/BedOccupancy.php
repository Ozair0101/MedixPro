<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `bed_occupancy` table.
 */
class BedOccupancy extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'bed_occupancy';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'bed_id', 'admission_id', 'patient_id',
        'occupied', 'status', 'cancelled', 'ward_id',
        'ward_sex_policy', 'patient_sex',
    ];

    protected $casts = [
        'cancelled' => 'boolean',
    ];
}
