<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `admission` table.
 */
class Admission extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'admission';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'patient_id', 'visit_id', 'admission_number',
        'admitted_at', 'discharged_at', 'admitting_practitioner_id', 'attending_practitioner_id',
        'admission_source', 'admission_type', 'discharge_outcome', 'discharge_summary',
        'death_time', 'status',
    ];

    protected $casts = [
        'admitted_at' => 'datetime',
        'discharged_at' => 'datetime',
        'death_time' => 'datetime',
    ];
}
