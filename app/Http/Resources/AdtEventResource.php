<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `adt_event`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class AdtEventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'admission_id' => $this->admission_id,
            'patient_id' => $this->patient_id,
            'event_type' => $this->event_type,
            'effective_at' => $this->effective_at?->toIso8601String(),
            'recorded_at' => $this->recorded_at?->toIso8601String(),
            'from_bed_id' => $this->from_bed_id,
            'to_bed_id' => $this->to_bed_id,
            'reason' => $this->reason,
            'reverses_event_id' => $this->reverses_event_id,
            'performed_by' => $this->performed_by,
        ];
    }
}
