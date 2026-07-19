<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `incident` table.
 */
class Incident extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'incident';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'incident_number', 'incident_type', 'severity',
        'patient_id', 'encounter_id', 'org_unit_id', 'occurred_at',
        'description', 'immediate_action', 'reported_by', 'is_anonymous',
        'reported_at', 'status', 'root_cause', 'corrective_action',
        'closed_by', 'closed_at',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'is_anonymous' => 'boolean',
        'reported_at' => 'datetime',
        'closed_at' => 'datetime',
    ];
}
