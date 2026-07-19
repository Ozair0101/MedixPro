<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `lab_critical_notification` table.
 */
class LabCriticalNotification extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'lab_critical_notification';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'result_id', 'notified_practitioner_id', 'notified_name',
        'notified_at', 'notified_by', 'method', 'read_back_confirmed',
        'notes',
    ];

    protected $casts = [
        'notified_at' => 'datetime',
        'read_back_confirmed' => 'boolean',
    ];
}
