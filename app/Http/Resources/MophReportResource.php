<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `moph_report`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class MophReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'form_type' => $this->form_type,
            'shamsi_month_id' => $this->shamsi_month_id,
            'shamsi_year' => $this->shamsi_year,
            'shamsi_month_no' => $this->shamsi_month_no,
            'reporting_period' => $this->reporting_period,
            'due_date' => $this->due_date,
            'status' => $this->status,
            'generated_at' => $this->generated_at?->toIso8601String(),
            'submitted_at' => $this->submitted_at?->toIso8601String(),
            'submitted_by' => $this->submitted_by,
            'submitted_to' => $this->submitted_to,
            'was_on_time' => $this->was_on_time,
            'rejection_reason' => $this->rejection_reason,
            'document_id' => $this->document_id,
        ];
    }
}
