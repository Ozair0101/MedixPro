<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `clinical_note` table.
 */
class ClinicalNote extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'clinical_note';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'patient_id', 'encounter_id', 'note_type',
        'body', 'status', 'previous_version_id', 'amendment_reason',
        'authored_by', 'authored_at',
    ];

    protected $casts = [
        'authored_at' => 'datetime',
    ];
}
