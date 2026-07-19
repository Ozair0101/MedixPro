<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `bed_unavailability` table.
 */
class BedUnavailability extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'bed_unavailability';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'bed_id', 'unavailable', 'reason',
        'recorded_by',
    ];
}
