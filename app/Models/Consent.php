<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `consent` table.
 */
class Consent extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'consent';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'patient_id', 'encounter_id', 'consent_type',
        'granted', 'granted_by_self', 'companion_id', 'witnessed_by',
        'document_id', 'granted_at', 'valid_until', 'withdrawn_at',
        'withdrawn_reason',
    ];

    protected $casts = [
        'granted' => 'boolean',
        'granted_by_self' => 'boolean',
        'granted_at' => 'datetime',
        'valid_until' => 'datetime',
        'withdrawn_at' => 'datetime',
    ];
}
