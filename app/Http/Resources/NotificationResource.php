<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `notification`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'channel' => $this->channel,
            'recipient_user_id' => $this->recipient_user_id,
            'recipient_phone' => $this->recipient_phone,
            'template_code' => $this->template_code,
            'body' => $this->body,
            'consent_verified' => $this->consent_verified,
            'status' => $this->status,
            'queued_at' => $this->queued_at?->toIso8601String(),
            'sent_at' => $this->sent_at?->toIso8601String(),
            'error' => $this->error,
        ];
    }
}
