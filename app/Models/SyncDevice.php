<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `sync_device` table.
 */
class SyncDevice extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'sync_device';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'device_label', 'assigned_user_id', 'scope_org_unit_id',
        'scope_district_pcode', 'last_sync_at', 'last_seen_seq', 'is_active',
    ];

    protected $casts = [
        'last_sync_at' => 'datetime',
        'last_seen_seq' => 'integer',
        'is_active' => 'boolean',
    ];
}
