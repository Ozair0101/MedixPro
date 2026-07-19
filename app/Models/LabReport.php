<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `lab_report` table.
 */
class LabReport extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'lab_report';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'sample_id', 'patient_id', 'status',
        'conclusion', 'rendered_document_id', 'issued_at', 'issued_by',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];
}
