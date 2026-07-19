<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `shift_pattern` table.
 */
class ShiftPattern extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'shift_pattern';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'code', 'name', 'start_time',
        'end_time', 'crosses_midnight',
    ];

    protected $casts = [
        'crosses_midnight' => 'boolean',
    ];
}
