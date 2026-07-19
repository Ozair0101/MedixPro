<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `medical_waste`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class MedicalWasteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'org_unit_id' => $this->org_unit_id,
            'waste_category' => $this->waste_category,
            'weight_kg' => $this->weight_kg,
            'collected_at' => $this->collected_at?->toIso8601String(),
            'disposal_method' => $this->disposal_method,
            'disposed_at' => $this->disposed_at?->toIso8601String(),
            'handled_by' => $this->handled_by,
        ];
    }
}
