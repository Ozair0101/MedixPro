<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `blood_donor` table.
 */
class BloodDonor extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'blood_donor';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'person_id', 'donor_number', 'blood_group',
        'is_permanently_deferred', 'deferred_until', 'deferral_reason', 'last_donation_date',
    ];

    protected $casts = [
        'is_permanently_deferred' => 'boolean',
        'deferred_until' => 'date',
        'last_donation_date' => 'date',
    ];
}
