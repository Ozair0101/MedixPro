<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `dhis2_export`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class Dhis2ExportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'report_id' => $this->report_id,
            'period' => $this->period,
            'payload' => $this->payload,
            'status' => $this->status,
            'generated_at' => $this->generated_at?->toIso8601String(),
            'sent_at' => $this->sent_at?->toIso8601String(),
            'response' => $this->response,
            'error' => $this->error,
        ];
    }
}
