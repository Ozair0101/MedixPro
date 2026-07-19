<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `stock_location` table.
 */
class StockLocation extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'stock_location';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'parent_id', 'code', 'name_local',
        'name_latin', 'location_type', 'counts_as_on_hand', 'is_dispensing_point',
        'org_unit_id', 'path', 'is_active',
    ];

    protected $casts = [
        'counts_as_on_hand' => 'boolean',
        'is_dispensing_point' => 'boolean',
        'is_active' => 'boolean',
    ];
}
