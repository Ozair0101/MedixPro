<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `cash_point` table.
 */
class CashPoint extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'cash_point';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'code', 'name', 'org_unit_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
