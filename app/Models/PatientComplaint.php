<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `patient_complaint` table.
 */
class PatientComplaint extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'patient_complaint';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'complaint_number', 'patient_id', 'complainant_name',
        'category', 'description', 'received_at', 'org_unit_id',
        'status', 'resolution', 'resolved_at',
    ];

    protected $casts = [
        'received_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];
}
