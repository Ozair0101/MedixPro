<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `clinical_procedure` table.
 */
class ClinicalProcedure extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'clinical_procedure';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'patient_id', 'encounter_id', 'order_id',
        'concept_id', 'status', 'status_reason', 'performed_period',
        'location_id', 'outcome', 'notes',
    ];
}
