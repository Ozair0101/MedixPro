<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `employee` table.
 */
class Employee extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'employee';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'person_id', 'practitioner_id', 'employee_number',
        'moph_staff_code', 'name_local', 'name_latin', 'gender',
        'org_unit_id', 'job_grade_id', 'job_title', 'staff_category',
        'employment_type', 'hired_on', 'terminated_on', 'termination_reason',
        'phone', 'bank_account', 'is_active',
    ];

    protected $casts = [
        'hired_on' => 'date',
        'terminated_on' => 'date',
        'is_active' => 'boolean',
    ];
}
