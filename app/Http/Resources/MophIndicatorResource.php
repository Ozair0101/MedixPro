<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `moph_indicator`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class MophIndicatorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name_local' => $this->name_local,
            'name_latin' => $this->name_latin,
            'domain' => $this->domain,
            'definition' => $this->definition,
            'numerator_spec' => $this->numerator_spec,
            'denominator_spec' => $this->denominator_spec,
            'data_source' => $this->data_source,
            'frequency' => $this->frequency,
            'is_computable_here' => $this->is_computable_here,
        ];
    }
}
