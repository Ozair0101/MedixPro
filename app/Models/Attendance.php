<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `attendance` table.
 */
class Attendance extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'attendance';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'employee_id', 'roster_assignment_id', 'attendance_date',
        'clock_in', 'clock_out', 'hours_worked', 'overtime_hours',
        'status', 'capture_method',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
        'hours_worked' => 'float',
        'overtime_hours' => 'float',
    ];
}
