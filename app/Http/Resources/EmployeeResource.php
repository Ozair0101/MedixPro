<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `employee`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'person_id' => $this->person_id,
            'practitioner_id' => $this->practitioner_id,
            'employee_number' => $this->employee_number,
            'moph_staff_code' => $this->moph_staff_code,
            'name_local' => $this->name_local,
            'name_latin' => $this->name_latin,
            'gender' => $this->gender,
            'org_unit_id' => $this->org_unit_id,
            'job_grade_id' => $this->job_grade_id,
            'job_title' => $this->job_title,
            'staff_category' => $this->staff_category,
            'employment_type' => $this->employment_type,
            'hired_on' => $this->hired_on,
            'terminated_on' => $this->terminated_on,
            'termination_reason' => $this->termination_reason,
            'phone' => $this->phone,
            'bank_account' => $this->bank_account,
            'is_active' => $this->is_active,
        ];
    }
}
