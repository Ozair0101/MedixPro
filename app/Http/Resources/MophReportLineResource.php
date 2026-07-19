<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `moph_report_line`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class MophReportLineResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'report_id' => $this->report_id,
            'section' => $this->section,
            'row_code' => $this->row_code,
            'row_label' => $this->row_label,
            'under5_male' => $this->under5_male,
            'under5_female' => $this->under5_female,
            'over5_male' => $this->over5_male,
            'over5_female' => $this->over5_female,
            'total' => $this->total,
            'numeric_value' => $this->numeric_value,
            'text_value' => $this->text_value,
            'manual_override' => $this->manual_override,
            'override_reason' => $this->override_reason,
        ];
    }
}
