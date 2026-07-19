<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `patient_relation` table.
 */
class PatientRelation extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'patient_relation';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'patient_id', 'related_person_id', 'name_local',
        'relationship', 'phone', 'is_emergency_contact', 'valid_period',
    ];

    protected $casts = [
        'is_emergency_contact' => 'boolean',
    ];
}
