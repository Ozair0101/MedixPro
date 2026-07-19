<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `payroll_run`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class PayrollRunResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'payroll_period_id' => $this->payroll_period_id,
            'employee_id' => $this->employee_id,
            'gross_salary' => $this->gross_salary,
            'overtime_amount' => $this->overtime_amount,
            'allowances' => $this->allowances,
            'deductions' => $this->deductions,
            'income_tax_withheld' => $this->income_tax_withheld,
            'net_pay' => $this->net_pay,
            'currency' => $this->currency,
            'payment_id' => $this->payment_id,
            'journal_entry_id' => $this->journal_entry_id,
        ];
    }
}
