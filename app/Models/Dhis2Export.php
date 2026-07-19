<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `dhis2_export` table.
 */
class Dhis2Export extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'dhis2_export';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'report_id', 'period', 'payload',
        'status', 'generated_at', 'sent_at', 'response',
        'error',
    ];

    protected $casts = [
        'payload' => 'array',
        'generated_at' => 'datetime',
        'sent_at' => 'datetime',
        'response' => 'array',
    ];
}
