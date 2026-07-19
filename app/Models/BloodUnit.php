<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `blood_unit` table.
 */
class BloodUnit extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'blood_unit';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'donation_id', 'unit_number', 'component',
        'blood_group', 'volume_ml', 'prepared_at', 'expires_at',
        'storage_location_id', 'status', 'discard_reason',
    ];

    protected $casts = [
        'volume_ml' => 'integer',
        'prepared_at' => 'datetime',
        'expires_at' => 'datetime',
    ];
}
