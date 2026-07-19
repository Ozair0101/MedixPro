<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `diagnostic_order`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class DiagnosticOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'specimen_type_concept_id' => $this->specimen_type_concept_id,
            'body_site_concept_id' => $this->body_site_concept_id,
            'laterality' => $this->laterality,
            'clinical_history' => $this->clinical_history,
            'reflex_from_order_id' => $this->reflex_from_order_id,
        ];
    }
}
