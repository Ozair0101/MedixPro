<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `imaging_study` table.
 */
class ImagingStudy extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'imaging_study';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'accession_number', 'patient_id', 'encounter_id',
        'order_id', 'modality_id', 'procedure_concept_id', 'body_site_concept_id',
        'laterality', 'status', 'scheduled_at', 'performed_at',
        'performed_by', 'contrast_used', 'contrast_agent', 'contrast_volume_ml',
        'radiation_dose_mgy', 'is_repeat', 'repeat_reason', 'dicom_study_uid',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'performed_at' => 'datetime',
        'contrast_used' => 'boolean',
        'contrast_volume_ml' => 'float',
        'radiation_dose_mgy' => 'float',
        'is_repeat' => 'boolean',
    ];
}
