<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `electronic_signature`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class ElectronicSignatureResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'target_table' => $this->target_table,
            'target_id' => $this->target_id,
            'target_version' => $this->target_version,
            'signer_id' => $this->signer_id,
            'purpose' => $this->purpose,
            'signed_at' => $this->signed_at?->toIso8601String(),
            'auth_method' => $this->auth_method,
            'content_hash' => $this->content_hash,
            'signed_manifestation' => $this->signed_manifestation,
            'ip_address' => $this->ip_address,
        ];
    }
}
