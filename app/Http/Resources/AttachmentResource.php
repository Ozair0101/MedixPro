<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `attachment`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class AttachmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'owner_type' => $this->owner_type,
            'owner_id' => $this->owner_id,
            'file_name' => $this->file_name,
            'mime_type' => $this->mime_type,
            'byte_size' => $this->byte_size,
            'storage_path' => $this->storage_path,
            'sha256' => $this->sha256,
            'is_sensitive' => $this->is_sensitive,
            'uploaded_by' => $this->uploaded_by,
            'uploaded_at' => $this->uploaded_at?->toIso8601String(),
        ];
    }
}
