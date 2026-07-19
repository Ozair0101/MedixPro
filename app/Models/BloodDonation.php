<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `blood_donation` table.
 */
class BloodDonation extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'blood_donation';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'donor_id', 'donation_number', 'donated_at',
        'volume_ml', 'haemoglobin_gdl', 'screened_hiv', 'screened_hbv',
        'screened_hcv', 'screened_syphilis', 'screened_malaria', 'screening_complete',
        'is_discarded', 'discard_reason',
    ];

    protected $casts = [
        'donated_at' => 'datetime',
        'volume_ml' => 'integer',
        'haemoglobin_gdl' => 'float',
        'screening_complete' => 'boolean',
        'is_discarded' => 'boolean',
    ];
}
