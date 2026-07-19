<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `asset_maintenance` table.
 */
class AssetMaintenance extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'asset_maintenance';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'asset_id', 'maintenance_type', 'scheduled_date',
        'performed_date', 'performed_by', 'cost', 'downtime_hours',
        'outcome', 'next_due_date', 'status',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'performed_date' => 'date',
        'cost' => 'float',
        'downtime_hours' => 'float',
        'next_due_date' => 'date',
    ];
}
