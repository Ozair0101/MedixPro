<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `attendance`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class AttendanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'roster_assignment_id' => $this->roster_assignment_id,
            'attendance_date' => $this->attendance_date,
            'clock_in' => $this->clock_in,
            'clock_out' => $this->clock_out,
            'hours_worked' => $this->hours_worked,
            'overtime_hours' => $this->overtime_hours,
            'status' => $this->status,
            'capture_method' => $this->capture_method,
        ];
    }
}
