<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `surgery` table.
 */
class Surgery extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'surgery';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'patient_id', 'encounter_id', 'procedure_id',
        'theatre_location_id', 'scheduled', 'actual', 'urgency',
        'asa_grade', 'anaesthesia_type', 'checklist_signin_at', 'checklist_timeout_at',
        'checklist_signout_at', 'estimated_blood_loss_ml', 'operative_note', 'status',
        'cancellation_reason',
    ];

    protected $casts = [
        'asa_grade' => 'integer',
        'checklist_signin_at' => 'datetime',
        'checklist_timeout_at' => 'datetime',
        'checklist_signout_at' => 'datetime',
        'estimated_blood_loss_ml' => 'integer',
    ];
}
