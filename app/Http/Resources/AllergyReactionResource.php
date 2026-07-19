<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `allergy_reaction`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class AllergyReactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'allergy_id' => $this->allergy_id,
            'severity' => $this->severity,
            'exposure_route_concept_id' => $this->exposure_route_concept_id,
            'occurred_at' => $this->occurred_at?->toIso8601String(),
            'description' => $this->description,
        ];
    }
}
