<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `blood_donor`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class BloodDonorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'person_id' => $this->person_id,
            'donor_number' => $this->donor_number,
            'blood_group' => $this->blood_group,
            'is_permanently_deferred' => $this->is_permanently_deferred,
            'deferred_until' => $this->deferred_until,
            'deferral_reason' => $this->deferral_reason,
            'last_donation_date' => $this->last_donation_date,
        ];
    }
}
