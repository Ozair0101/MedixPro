<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `allergy_manifestation`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class AllergyManifestationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'reaction_id' => $this->reaction_id,
            'concept_id' => $this->concept_id,
        ];
    }
}
