<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `mar_slot` table.
 */
class MarSlot extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'mar_slot';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'drug_order_id', 'patient_id', 'admission_id',
        'scheduled_at', 'window_start', 'window_end', 'is_prn',
        'slot_status', 'hold_reason', 'administration_id',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'window_start' => 'datetime',
        'window_end' => 'datetime',
        'is_prn' => 'boolean',
    ];
}
