<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `shamsi_month` table.
 */
class ShamsiMonth extends Model
{
    use HasUuids;

    protected $table = 'shamsi_month';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'shamsi_year', 'month_no', 'name_dari', 'name_latin',
        'name_pashto', 'gregorian_period', 'reporting_gregorian_month', 'fiscal_year_id',
    ];

    protected $casts = [
        'shamsi_year' => 'integer',
        'month_no' => 'integer',
        'reporting_gregorian_month' => 'date',
    ];
}
