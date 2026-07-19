<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `census_snapshot`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class CensusSnapshotResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'census_date' => $this->census_date,
            'ward_id' => $this->ward_id,
            'occupied_beds' => $this->occupied_beds,
            'available_beds' => $this->available_beds,
            'admissions' => $this->admissions,
            'discharges' => $this->discharges,
            'deaths' => $this->deaths,
            'transfers_in' => $this->transfers_in,
            'transfers_out' => $this->transfers_out,
            'computed_at' => $this->computed_at?->toIso8601String(),
        ];
    }
}
