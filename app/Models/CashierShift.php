<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `cashier_shift` table.
 */
class CashierShift extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'cashier_shift';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'cash_point_id', 'cashier_id', 'opened_at',
        'opening_float', 'closed_at', 'declared_cash', 'expected_cash',
        'variance_reason', 'closed_by', 'status',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'opening_float' => 'float',
        'closed_at' => 'datetime',
        'declared_cash' => 'float',
        'expected_cash' => 'float',
    ];
}
