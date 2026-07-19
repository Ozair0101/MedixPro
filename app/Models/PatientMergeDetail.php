<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `patient_merge_detail` table.
 */
class PatientMergeDetail extends Model
{
    use HasUuids;

    protected $table = 'patient_merge_detail';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'patient_link_id', 'table_name', 'record_id', 'from_patient_id',
        'to_patient_id',
    ];
}
