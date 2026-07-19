<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `asset` table.
 */
class Asset extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'asset';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'asset_tag', 'name', 'category',
        'manufacturer', 'model', 'serial_number', 'location_id',
        'org_unit_id', 'purchase_date', 'purchase_cost', 'warranty_expires',
        'status', 'is_donated', 'donor_name',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_cost' => 'float',
        'warranty_expires' => 'date',
        'is_donated' => 'boolean',
    ];
}
