<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `appointment`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class AppointmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'org_unit_id' => $this->org_unit_id,
            'practitioner_id' => $this->practitioner_id,
            'slot' => $this->slot,
            'status' => $this->status,
            'reason' => $this->reason,
            'booked_at' => $this->booked_at?->toIso8601String(),
            'booked_by' => $this->booked_by,
            'cancelled_reason' => $this->cancelled_reason,
            'rescheduled_to_id' => $this->rescheduled_to_id,
            'encounter_id' => $this->encounter_id,
        ];
    }
}
