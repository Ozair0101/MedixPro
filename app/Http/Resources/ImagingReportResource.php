<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `imaging_report`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class ImagingReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'study_id' => $this->study_id,
            'findings' => $this->findings,
            'impression' => $this->impression,
            'status' => $this->status,
            'previous_version_id' => $this->previous_version_id,
            'amendment_reason' => $this->amendment_reason,
            'reported_by' => $this->reported_by,
            'reported_at' => $this->reported_at?->toIso8601String(),
            'verified_by' => $this->verified_by,
            'verified_at' => $this->verified_at?->toIso8601String(),
            'rendered_document_id' => $this->rendered_document_id,
        ];
    }
}
