<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `blood_crossmatch` table.
 */
class BloodCrossmatch extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'blood_crossmatch';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'patient_id', 'encounter_id', 'order_id',
        'blood_unit_id', 'patient_blood_group', 'result', 'method',
        'performed_by', 'performed_at', 'expires_at',
    ];

    protected $casts = [
        'performed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];
}
