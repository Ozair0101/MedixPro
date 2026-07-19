<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `clinical_order`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class ClinicalOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'requisition_number' => $this->requisition_number,
            'patient_id' => $this->patient_id,
            'encounter_id' => $this->encounter_id,
            'order_type_code' => $this->order_type_code,
            'concept_id' => $this->concept_id,
            'status' => $this->status,
            'intent' => $this->intent,
            'priority' => $this->priority,
            'order_action' => $this->order_action,
            'previous_order_id' => $this->previous_order_id,
            'ordered_by' => $this->ordered_by,
            'ordered_at' => $this->ordered_at?->toIso8601String(),
            'is_verbal_order' => $this->is_verbal_order,
            'countersigned_by' => $this->countersigned_by,
            'countersigned_at' => $this->countersigned_at?->toIso8601String(),
            'scheduled_at' => $this->scheduled_at?->toIso8601String(),
            'instructions' => $this->instructions,
            'order_reason' => $this->order_reason,
            'stopped_at' => $this->stopped_at?->toIso8601String(),
            'fulfiller_status' => $this->fulfiller_status,
        ];
    }
}
