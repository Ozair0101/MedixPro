<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `lab_sample_item`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class LabSampleItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sample_id' => $this->sample_id,
            'parent_item_id' => $this->parent_item_id,
            'specimen_type_concept_id' => $this->specimen_type_concept_id,
            'container_type' => $this->container_type,
            'quantity' => $this->quantity,
            'quantity_unit' => $this->quantity_unit,
            'collected_at' => $this->collected_at?->toIso8601String(),
            'collected_by' => $this->collected_by,
            'collection_method' => $this->collection_method,
            'collection_conditions' => $this->collection_conditions,
            'temperature_c' => $this->temperature_c,
            'received_at' => $this->received_at?->toIso8601String(),
            'rejected' => $this->rejected,
            'rejection_reason' => $this->rejection_reason,
            'barcode' => $this->barcode,
        ];
    }
}
