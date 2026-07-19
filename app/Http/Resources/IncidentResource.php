<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `incident`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class IncidentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'incident_number' => $this->incident_number,
            'incident_type' => $this->incident_type,
            'severity' => $this->severity,
            'patient_id' => $this->patient_id,
            'encounter_id' => $this->encounter_id,
            'org_unit_id' => $this->org_unit_id,
            'occurred_at' => $this->occurred_at?->toIso8601String(),
            'description' => $this->description,
            'immediate_action' => $this->immediate_action,
            'reported_by' => $this->reported_by,
            'is_anonymous' => $this->is_anonymous,
            'reported_at' => $this->reported_at?->toIso8601String(),
            'status' => $this->status,
            'root_cause' => $this->root_cause,
            'corrective_action' => $this->corrective_action,
            'closed_by' => $this->closed_by,
            'closed_at' => $this->closed_at?->toIso8601String(),
        ];
    }
}
