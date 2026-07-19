<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `asset_maintenance`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class AssetMaintenanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'asset_id' => $this->asset_id,
            'maintenance_type' => $this->maintenance_type,
            'scheduled_date' => $this->scheduled_date,
            'performed_date' => $this->performed_date,
            'performed_by' => $this->performed_by,
            'cost' => $this->cost,
            'downtime_hours' => $this->downtime_hours,
            'outcome' => $this->outcome,
            'next_due_date' => $this->next_due_date,
            'status' => $this->status,
        ];
    }
}
