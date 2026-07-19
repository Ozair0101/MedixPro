<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `sync_event` table.
 *
 * APPEND-ONLY. Database triggers reject UPDATE and DELETE; do not add
 * mutation methods here. Corrections are new rows, not edits.
 */
class SyncEvent extends Model
{
    use BelongsToFacility;

    protected $table = 'sync_event';

    protected $primaryKey = 'seq';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'device_id', 'event_id', 'table_name',
        'record_id', 'operation', 'payload', 'client_ts',
        'server_ts', 'applied', 'apply_error',
    ];

    protected $casts = [
        'payload' => 'array',
        'client_ts' => 'datetime',
        'server_ts' => 'datetime',
        'applied' => 'boolean',
    ];
}
