<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `employment_contract`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class EmploymentContractResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'contract_number' => $this->contract_number,
            'gross_salary' => $this->gross_salary,
            'currency' => $this->currency,
            'contract_period' => $this->contract_period,
            'funding_source' => $this->funding_source,
        ];
    }
}
