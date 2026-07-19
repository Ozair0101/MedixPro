<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `lab_critical_notification`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class LabCriticalNotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'result_id' => $this->result_id,
            'notified_practitioner_id' => $this->notified_practitioner_id,
            'notified_name' => $this->notified_name,
            'notified_at' => $this->notified_at?->toIso8601String(),
            'notified_by' => $this->notified_by,
            'method' => $this->method,
            'read_back_confirmed' => $this->read_back_confirmed,
            'notes' => $this->notes,
        ];
    }
}
