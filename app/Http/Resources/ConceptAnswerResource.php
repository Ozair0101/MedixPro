<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `concept_answer`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class ConceptAnswerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'concept_id' => $this->concept_id,
            'answer_concept_id' => $this->answer_concept_id,
            'sort_weight' => $this->sort_weight,
        ];
    }
}
