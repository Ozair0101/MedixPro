<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `billable_item_code`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class BillableItemCodeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'item_id' => $this->item_id,
            'code_system' => $this->code_system,
            'code' => $this->code,
            'valid_at' => $this->valid_at?->toIso8601String(),
        ];
    }
}
