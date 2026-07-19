<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `employment_contract` table.
 */
class EmploymentContract extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'employment_contract';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'employee_id', 'contract_number', 'gross_salary',
        'currency', 'contract_period', 'funding_source',
    ];

    protected $casts = [
        'gross_salary' => 'float',
    ];
}
