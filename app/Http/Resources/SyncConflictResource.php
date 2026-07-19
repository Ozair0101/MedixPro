<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `sync_conflict`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class SyncConflictResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'table_name' => $this->table_name,
            'record_id' => $this->record_id,
            'field_name' => $this->field_name,
            'server_value' => $this->server_value,
            'client_value' => $this->client_value,
            'client_event_id' => $this->client_event_id,
            'status' => $this->status,
            'resolved_value' => $this->resolved_value,
            'resolved_by' => $this->resolved_by,
            'resolved_at' => $this->resolved_at?->toIso8601String(),
            'detected_at' => $this->detected_at?->toIso8601String(),
        ];
    }
}
