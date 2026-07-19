<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `journal_entry`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class JournalEntryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'entry_number' => $this->entry_number,
            'period_id' => $this->period_id,
            'posting_date' => $this->posting_date,
            'source_type' => $this->source_type,
            'source_id' => $this->source_id,
            'posting_rule_id' => $this->posting_rule_id,
            'description' => $this->description,
            'status' => $this->status,
            'reverses_id' => $this->reverses_id,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
