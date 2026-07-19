<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Vital signs — a TYPED table, not EAV.
 *
 * This is the ADR-011 split in practice: anything where a wrong value can hurt
 * someone gets real columns and real CHECK constraints. The database rejects a
 * systolic of 400 or a temperature of 12 outright, so a slipped decimal cannot
 * reach a chart and be read as real.
 *
 * The constraints are PLAUSIBILITY bounds, not reference ranges: they reject
 * typos, never abnormal patients.
 */
class Vitals extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'vitals';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'patient_id', 'encounter_id', 'recorded_at',
        'temperature_c', 'pulse_bpm', 'respiratory_rate',
        'systolic_bp', 'diastolic_bp', 'spo2_percent',
        'weight_kg', 'height_cm', 'muac_cm', 'head_circumference_cm',
        'pain_score', 'consciousness', 'glasgow_coma_score', 'recorded_by',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'temperature_c' => 'float',
        'weight_kg' => 'float',
        'height_cm' => 'float',
        'muac_cm' => 'float',
        'bmi' => 'float',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Encounter::class);
    }

    /**
     * Values that warrant immediate attention.
     *
     * Deliberately conservative and adult-oriented; paediatric thresholds differ
     * by age and belong in concept_reference_range, not hardcoded here.
     */
    public function criticalFlags(): array
    {
        $flags = [];

        if ($this->spo2_percent !== null && $this->spo2_percent < 90) {
            $flags[] = 'hypoxia';
        }
        if ($this->systolic_bp !== null && $this->systolic_bp < 90) {
            $flags[] = 'hypotension';
        }
        if ($this->systolic_bp !== null && $this->systolic_bp >= 180) {
            $flags[] = 'severe_hypertension';
        }
        if ($this->temperature_c !== null && $this->temperature_c >= 39.0) {
            $flags[] = 'high_fever';
        }
        if ($this->temperature_c !== null && $this->temperature_c < 35.0) {
            $flags[] = 'hypothermia';
        }
        if ($this->pulse_bpm !== null && ($this->pulse_bpm < 50 || $this->pulse_bpm > 130)) {
            $flags[] = 'abnormal_pulse';
        }
        if ($this->glasgow_coma_score !== null && $this->glasgow_coma_score <= 8) {
            $flags[] = 'reduced_consciousness';
        }
        // MUAC < 11.5cm in a child indicates severe acute malnutrition and
        // drives the MoPH nutrition sections of MIAR/HMIR.
        if ($this->muac_cm !== null && $this->muac_cm < 11.5) {
            $flags[] = 'severe_acute_malnutrition';
        }

        return $flags;
    }
}
