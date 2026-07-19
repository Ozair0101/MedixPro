<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `blood_donation`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class BloodDonationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'donor_id' => $this->donor_id,
            'donation_number' => $this->donation_number,
            'donated_at' => $this->donated_at?->toIso8601String(),
            'volume_ml' => $this->volume_ml,
            'haemoglobin_gdl' => $this->haemoglobin_gdl,
            'screened_hiv' => $this->screened_hiv,
            'screened_hbv' => $this->screened_hbv,
            'screened_hcv' => $this->screened_hcv,
            'screened_syphilis' => $this->screened_syphilis,
            'screened_malaria' => $this->screened_malaria,
            'screening_complete' => $this->screening_complete,
            'is_discarded' => $this->is_discarded,
            'discard_reason' => $this->discard_reason,
        ];
    }
}
