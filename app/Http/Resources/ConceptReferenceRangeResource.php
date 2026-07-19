<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `concept_reference_range`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class ConceptReferenceRangeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'concept_id' => $this->concept_id,
            'sex' => $this->sex,
            'min_age_days' => $this->min_age_days,
            'max_age_days' => $this->max_age_days,
            'low_normal' => $this->low_normal,
            'high_normal' => $this->high_normal,
            'low_valid' => $this->low_valid,
            'high_valid' => $this->high_valid,
            'low_critical' => $this->low_critical,
            'high_critical' => $this->high_critical,
            'low_reporting' => $this->low_reporting,
            'high_reporting' => $this->high_reporting,
            'valid_period' => $this->valid_period,
        ];
    }
}
