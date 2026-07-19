<?php

namespace App\Http\Resources\Pharmacy;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'generic_name' => $this->generic_name,
            'strength' => $this->strength,
            'form' => $this->form,
            'standard_dose' => $this->standard_dose,
            'controlled' => (bool) $this->controlled,
            'min_stock' => (int) $this->min_stock,

            // Only present when the caller eager-loaded them, so the resource
            // never triggers an N+1 by itself.
            'batches' => $this->whenLoaded('batches'),

            // hospital_id is deliberately NOT exposed. It is server-side tenancy
            // state; echoing it back invites clients to treat it as writable.
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
