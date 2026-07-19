<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `payroll_run` table.
 */
class PayrollRun extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'payroll_run';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'payroll_period_id', 'employee_id', 'gross_salary',
        'overtime_amount', 'allowances', 'deductions', 'income_tax_withheld',
        'net_pay', 'currency', 'payment_id', 'journal_entry_id',
    ];

    protected $casts = [
        'gross_salary' => 'float',
        'overtime_amount' => 'float',
        'allowances' => 'float',
        'deductions' => 'float',
        'income_tax_withheld' => 'float',
        'net_pay' => 'float',
    ];
}
