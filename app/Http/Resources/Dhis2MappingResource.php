<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `dhis2_mapping`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class Dhis2MappingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'source_type' => $this->source_type,
            'source_key' => $this->source_key,
            'data_element_uid' => $this->data_element_uid,
            'category_option_combo_uid' => $this->category_option_combo_uid,
            'org_unit_uid' => $this->org_unit_uid,
        ];
    }
}
