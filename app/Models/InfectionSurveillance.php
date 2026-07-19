<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `infection_surveillance` table.
 */
class InfectionSurveillance extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'infection_surveillance';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'patient_id', 'admission_id', 'infection_type',
        'is_healthcare_associated', 'present_on_admission', 'organism', 'antibiogram',
        'detected_at', 'lab_analysis_id', 'outcome', 'reported_by',
    ];

    protected $casts = [
        'is_healthcare_associated' => 'boolean',
        'present_on_admission' => 'boolean',
        'antibiogram' => 'array',
        'detected_at' => 'datetime',
    ];
}
