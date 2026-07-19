<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `sterilization_usage` table.
 */
class SterilizationUsage extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'sterilization_usage';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'cycle_id', 'set_id', 'surgery_id',
        'patient_id', 'used_at',
    ];

    protected $casts = [
        'used_at' => 'datetime',
    ];
}
