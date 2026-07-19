<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `sync_conflict` table.
 */
class SyncConflict extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'sync_conflict';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'table_name', 'record_id', 'field_name',
        'server_value', 'client_value', 'client_event_id', 'status',
        'resolved_value', 'resolved_by', 'resolved_at', 'detected_at',
    ];

    protected $casts = [
        'server_value' => 'array',
        'client_value' => 'array',
        'resolved_value' => 'array',
        'resolved_at' => 'datetime',
        'detected_at' => 'datetime',
    ];
}
