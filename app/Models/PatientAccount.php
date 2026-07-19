<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `patient_account` table.
 */
class PatientAccount extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'patient_account';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'account_number', 'patient_id', 'encounter_id',
        'admission_id', 'kind', 'status', 'billing_status',
        'guarantor_party_id', 'service_period', 'currency', 'opened_at',
        'closed_at',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];
}
