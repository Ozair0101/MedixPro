<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `lab_test_result_option`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class LabTestResultOptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'test_id' => $this->test_id,
            'value_concept_id' => $this->value_concept_id,
            'value_text' => $this->value_text,
            'is_normal' => $this->is_normal,
            'sort_order' => $this->sort_order,
        ];
    }
}
