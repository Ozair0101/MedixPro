<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `sterilization_cycle`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class SterilizationCycleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cycle_number' => $this->cycle_number,
            'sterilizer_asset_id' => $this->sterilizer_asset_id,
            'method' => $this->method,
            'temperature_c' => $this->temperature_c,
            'pressure_bar' => $this->pressure_bar,
            'duration_minutes' => $this->duration_minutes,
            'started_at' => $this->started_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'chemical_indicator_pass' => $this->chemical_indicator_pass,
            'biological_indicator_pass' => $this->biological_indicator_pass,
            'released' => $this->released,
            'released_by' => $this->released_by,
            'recalled' => $this->recalled,
            'recall_reason' => $this->recall_reason,
        ];
    }
}
