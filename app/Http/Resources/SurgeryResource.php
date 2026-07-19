<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `surgery`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class SurgeryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'encounter_id' => $this->encounter_id,
            'procedure_id' => $this->procedure_id,
            'theatre_location_id' => $this->theatre_location_id,
            'scheduled' => $this->scheduled,
            'actual' => $this->actual,
            'urgency' => $this->urgency,
            'asa_grade' => $this->asa_grade,
            'anaesthesia_type' => $this->anaesthesia_type,
            'checklist_signin_at' => $this->checklist_signin_at?->toIso8601String(),
            'checklist_timeout_at' => $this->checklist_timeout_at?->toIso8601String(),
            'checklist_signout_at' => $this->checklist_signout_at?->toIso8601String(),
            'estimated_blood_loss_ml' => $this->estimated_blood_loss_ml,
            'operative_note' => $this->operative_note,
            'status' => $this->status,
            'cancellation_reason' => $this->cancellation_reason,
        ];
    }
}
