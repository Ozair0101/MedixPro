<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `practitioner_qualification`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class PractitionerQualificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'practitioner_id' => $this->practitioner_id,
            'qualification' => $this->qualification,
            'issuing_body' => $this->issuing_body,
            'licence_number' => $this->licence_number,
            'valid_period' => $this->valid_period,
        ];
    }
}
