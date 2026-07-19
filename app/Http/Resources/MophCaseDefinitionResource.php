<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `moph_case_definition`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class MophCaseDefinitionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'priority_condition_id' => $this->priority_condition_id,
            'definition_local' => $this->definition_local,
            'definition_latin' => $this->definition_latin,
            'source_reference' => $this->source_reference,
        ];
    }
}
