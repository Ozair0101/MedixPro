<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `audit_log` table.
 *
 * APPEND-ONLY. Database triggers reject UPDATE and DELETE; do not add
 * mutation methods here. Corrections are new rows, not edits.
 */
class AuditLog extends Model
{
    use BelongsToFacility;

    protected $table = 'audit_log';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'occurred_at', 'user_id', 'username',
        'action', 'table_name', 'record_id', 'patient_id',
        'old_values', 'new_values', 'ip_address', 'user_agent',
        'override_reason',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'old_values' => 'array',
        'new_values' => 'array',
    ];
}
