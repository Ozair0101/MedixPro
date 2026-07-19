<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `bed` table.
 */
class Bed extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'bed';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'ward_id', 'bed_type_id', 'bed_number',
        'is_isolation', 'has_oxygen', 'operational_status',
    ];

    protected $casts = [
        'is_isolation' => 'boolean',
        'has_oxygen' => 'boolean',
    ];
}
