<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `party`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class PartyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'party_type' => $this->party_type,
            'person_id' => $this->person_id,
            'organization_name' => $this->organization_name,
            'tin' => $this->tin,
            'phone' => $this->phone,
            'address' => $this->address,
        ];
    }
}
