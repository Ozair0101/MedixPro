<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `concept`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class ConceptResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'class_code' => $this->class_code,
            'datatype_code' => $this->datatype_code,
            'short_name' => $this->short_name,
            'description' => $this->description,
            'is_set' => $this->is_set,
            'retired' => $this->retired,
            'retire_reason' => $this->retire_reason,
            'version' => $this->version,
        ];
    }
}
