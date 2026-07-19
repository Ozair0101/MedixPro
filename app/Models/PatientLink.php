<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Records a merge between two patient records.
 *
 * Merging by UPDATE-then-DELETE destroys history and breaks every external
 * reference holding the old MRN — printed cards, referral letters, lab
 * requisitions. Instead the losing record is deactivated and linked, and reads
 * resolve forward through `replaced_by`.
 *
 * `reversed_at` is what makes a merge undoable. Two patients wrongly combined
 * must be separable again, and patient_merge_detail records exactly which rows
 * moved so the reversal is mechanical rather than guesswork.
 */
class PatientLink extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'patient_link';

    // merged_at is set by the database default.
    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'patient_id', 'other_patient_id', 'link_type',
        'merged_by', 'merge_reason', 'reversed_at', 'reversed_by',
    ];

    protected $casts = [
        'merged_at' => 'datetime',
        'reversed_at' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function other(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'other_patient_id');
    }
}
