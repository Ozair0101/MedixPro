<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `account_coverage`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class AccountCoverageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'account_id' => $this->account_id,
            'coverage_id' => $this->coverage_id,
            'priority' => $this->priority,
        ];
    }
}
