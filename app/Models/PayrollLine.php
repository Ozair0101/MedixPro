<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `payroll_line` table.
 */
class PayrollLine extends Model
{
    use HasUuids;

    protected $table = 'payroll_line';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'payroll_run_id', 'line_type', 'description', 'amount',
    ];

    protected $casts = [
        'amount' => 'float',
    ];
}
