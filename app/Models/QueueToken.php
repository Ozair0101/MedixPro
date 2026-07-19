<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `queue_token` table.
 */
class QueueToken extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'queue_token';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'org_unit_id', 'patient_id', 'encounter_id',
        'token_code', 'queue_date', 'priority', 'status',
        'issued_at', 'called_at', 'served_at',
    ];

    protected $casts = [
        'queue_date' => 'date',
        'issued_at' => 'datetime',
        'called_at' => 'datetime',
        'served_at' => 'datetime',
    ];
}
