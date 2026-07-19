<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `concept_set_member`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class ConceptSetMemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'set_concept_id' => $this->set_concept_id,
            'member_concept_id' => $this->member_concept_id,
            'sort_weight' => $this->sort_weight,
        ];
    }
}
