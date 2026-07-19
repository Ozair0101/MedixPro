<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `drug_administration`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class DrugAdministrationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'mar_slot_id' => $this->mar_slot_id,
            'drug_order_id' => $this->drug_order_id,
            'patient_id' => $this->patient_id,
            'dispense_id' => $this->dispense_id,
            'stock_lot_id' => $this->stock_lot_id,
            'dose_given' => $this->dose_given,
            'dose_unit_concept_id' => $this->dose_unit_concept_id,
            'route_concept_id' => $this->route_concept_id,
            'site' => $this->site,
            'administered_at' => $this->administered_at?->toIso8601String(),
            'administered_by' => $this->administered_by,
            'witnessed_by' => $this->witnessed_by,
            'status' => $this->status,
            'not_done_reason' => $this->not_done_reason,
            'notes' => $this->notes,
        ];
    }
}
