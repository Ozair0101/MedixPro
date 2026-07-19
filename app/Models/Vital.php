<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `vitals` table.
 */
class Vital extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'vitals';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'patient_id', 'encounter_id', 'recorded_at',
        'temperature_c', 'pulse_bpm', 'respiratory_rate', 'systolic_bp',
        'diastolic_bp', 'spo2_percent', 'weight_kg', 'height_cm',
        'muac_cm', 'head_circumference_cm', 'pain_score', 'consciousness',
        'glasgow_coma_score', 'recorded_by',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'temperature_c' => 'float',
        'pulse_bpm' => 'integer',
        'respiratory_rate' => 'integer',
        'systolic_bp' => 'integer',
        'diastolic_bp' => 'integer',
        'spo2_percent' => 'integer',
        'weight_kg' => 'float',
        'height_cm' => 'float',
        'muac_cm' => 'float',
        'head_circumference_cm' => 'float',
        'pain_score' => 'integer',
        'glasgow_coma_score' => 'integer',
    ];
}
