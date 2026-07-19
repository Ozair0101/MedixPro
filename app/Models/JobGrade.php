<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `job_grade` table.
 */
class JobGrade extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'job_grade';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'code', 'name', 'base_salary',
    ];

    protected $casts = [
        'base_salary' => 'float',
    ];
}
