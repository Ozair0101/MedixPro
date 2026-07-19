<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `number_series`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class NumberSeriesResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'series_code' => $this->series_code,
            'prefix' => $this->prefix,
            'suffix' => $this->suffix,
            'padding' => $this->padding,
            'next_value' => $this->next_value,
            'resets_annually' => $this->resets_annually,
            'current_period' => $this->current_period,
        ];
    }
}
