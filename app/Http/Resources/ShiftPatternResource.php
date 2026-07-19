<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `shift_pattern`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class ShiftPatternResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'crosses_midnight' => $this->crosses_midnight,
        ];
    }
}
