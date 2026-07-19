<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `ambulance` table.
 */
class Ambulance extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'ambulance';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'asset_id', 'call_sign', 'vehicle_type',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];
}
