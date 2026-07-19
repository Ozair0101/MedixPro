<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `adt_event` table.
 *
 * APPEND-ONLY. Database triggers reject UPDATE and DELETE; do not add
 * mutation methods here. Corrections are new rows, not edits.
 */
class AdtEvent extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'adt_event';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'admission_id', 'patient_id', 'event_type',
        'effective_at', 'recorded_at', 'from_bed_id', 'to_bed_id',
        'reason', 'reverses_event_id', 'performed_by',
    ];

    protected $casts = [
        'effective_at' => 'datetime',
        'recorded_at' => 'datetime',
    ];
}
