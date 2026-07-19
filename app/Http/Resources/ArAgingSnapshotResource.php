<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `ar_aging_snapshot`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class ArAgingSnapshotResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'as_of_date' => $this->as_of_date,
            'account_id' => $this->account_id,
            'responsible_party_type' => $this->responsible_party_type,
            'responsible_party_id' => $this->responsible_party_id,
            'bucket_id' => $this->bucket_id,
            'outstanding' => $this->outstanding,
        ];
    }
}
