<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `data_export`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class DataExportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'export_type' => $this->export_type,
            'format' => $this->format,
            'requested_by' => $this->requested_by,
            'requested_at' => $this->requested_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'row_count' => $this->row_count,
            'byte_size' => $this->byte_size,
            'storage_path' => $this->storage_path,
            'sha256' => $this->sha256,
            'status' => $this->status,
        ];
    }
}
