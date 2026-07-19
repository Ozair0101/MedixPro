<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `dews_notification`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class DewsNotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'encounter_id' => $this->encounter_id,
            'priority_condition_id' => $this->priority_condition_id,
            'case_classification' => $this->case_classification,
            'onset_date' => $this->onset_date,
            'detected_at' => $this->detected_at?->toIso8601String(),
            'reported_at' => $this->reported_at?->toIso8601String(),
            'reported_by' => $this->reported_by,
            'channel' => $this->channel,
            'outcome' => $this->outcome,
            'status' => $this->status,
        ];
    }
}
