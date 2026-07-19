<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `lab_report`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class LabReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sample_id' => $this->sample_id,
            'patient_id' => $this->patient_id,
            'status' => $this->status,
            'conclusion' => $this->conclusion,
            'rendered_document_id' => $this->rendered_document_id,
            'issued_at' => $this->issued_at?->toIso8601String(),
            'issued_by' => $this->issued_by,
        ];
    }
}
