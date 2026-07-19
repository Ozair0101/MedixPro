<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `shamsi_month`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class ShamsiMonthResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'shamsi_year' => $this->shamsi_year,
            'month_no' => $this->month_no,
            'name_dari' => $this->name_dari,
            'name_latin' => $this->name_latin,
            'name_pashto' => $this->name_pashto,
            'gregorian_period' => $this->gregorian_period,
            'reporting_gregorian_month' => $this->reporting_gregorian_month,
            'fiscal_year_id' => $this->fiscal_year_id,
        ];
    }
}
