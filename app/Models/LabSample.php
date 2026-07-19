<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `lab_sample` table.
 */
class LabSample extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'lab_sample';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'accession_number', 'patient_id', 'encounter_id',
        'requisition_number', 'priority', 'status', 'ordered_by',
        'entered_at', 'collected_at', 'received_at', 'completed_at',
    ];

    protected $casts = [
        'entered_at' => 'datetime',
        'collected_at' => 'datetime',
        'received_at' => 'datetime',
        'completed_at' => 'datetime',
    ];
}
