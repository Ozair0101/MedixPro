<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `dews_notification` table.
 */
class DewsNotification extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'dews_notification';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'patient_id', 'encounter_id', 'priority_condition_id',
        'case_classification', 'onset_date', 'detected_at', 'reported_at',
        'reported_by', 'channel', 'outcome', 'status',
    ];

    protected $casts = [
        'onset_date' => 'date',
        'detected_at' => 'datetime',
        'reported_at' => 'datetime',
    ];
}
