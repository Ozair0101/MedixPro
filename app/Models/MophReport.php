<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `moph_report` table.
 */
class MophReport extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'moph_report';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'form_type', 'shamsi_month_id', 'shamsi_year',
        'shamsi_month_no', 'reporting_period', 'due_date', 'status',
        'generated_at', 'submitted_at', 'submitted_by', 'submitted_to',
        'was_on_time', 'rejection_reason', 'document_id',
    ];

    protected $casts = [
        'shamsi_year' => 'integer',
        'shamsi_month_no' => 'integer',
        'due_date' => 'date',
        'generated_at' => 'datetime',
        'submitted_at' => 'datetime',
        'was_on_time' => 'boolean',
    ];
}
