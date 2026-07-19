<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `hand_hygiene_audit` table.
 */
class HandHygieneAudit extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'hand_hygiene_audit';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'org_unit_id', 'audit_date', 'opportunities',
        'compliant', 'observed_by',
    ];

    protected $casts = [
        'audit_date' => 'date',
        'opportunities' => 'integer',
        'compliant' => 'integer',
    ];
}
