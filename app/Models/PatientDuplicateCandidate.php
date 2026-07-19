<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `patient_duplicate_candidate` table.
 */
class PatientDuplicateCandidate extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'patient_duplicate_candidate';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'patient_a_id', 'patient_b_id', 'score',
        'matched_on', 'status', 'reviewed_by', 'reviewed_at',
        'detected_at',
    ];

    protected $casts = [
        'score' => 'float',
        'reviewed_at' => 'datetime',
        'detected_at' => 'datetime',
    ];
}
