<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `practitioner_specialty`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class PractitionerSpecialtyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'practitioner_id' => $this->practitioner_id,
            'specialty_code' => $this->specialty_code,
        ];
    }
}
