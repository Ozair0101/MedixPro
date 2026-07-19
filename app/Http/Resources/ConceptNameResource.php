<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `concept_name`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class ConceptNameResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'concept_id' => $this->concept_id,
            'name' => $this->name,
            'locale' => $this->locale,
            'is_preferred' => $this->is_preferred,
            'name_type' => $this->name_type,
        ];
    }
}
