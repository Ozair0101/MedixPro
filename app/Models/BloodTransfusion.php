<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `blood_transfusion` table.
 */
class BloodTransfusion extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'blood_transfusion';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'patient_id', 'encounter_id', 'blood_unit_id',
        'crossmatch_id', 'started_at', 'completed_at', 'volume_transfused_ml',
        'administered_by', 'verified_by', 'status',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'volume_transfused_ml' => 'integer',
    ];
}
