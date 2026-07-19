<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `sync_device`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class SyncDeviceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'device_label' => $this->device_label,
            'assigned_user_id' => $this->assigned_user_id,
            'scope_org_unit_id' => $this->scope_org_unit_id,
            'scope_district_pcode' => $this->scope_district_pcode,
            'last_sync_at' => $this->last_sync_at?->toIso8601String(),
            'last_seen_seq' => $this->last_seen_seq,
            'is_active' => $this->is_active,
        ];
    }
}
