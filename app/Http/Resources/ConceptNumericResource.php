<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `concept_numeric`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class ConceptNumericResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'concept_id' => $this->concept_id,
            'unit_concept_id' => $this->unit_concept_id,
            'allow_decimal' => $this->allow_decimal,
            'display_precision' => $this->display_precision,
        ];
    }
}
