<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `app_user`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class AppUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'password_hash' => $this->password_hash,
            'display_name' => $this->display_name,
            'gender' => $this->gender,
            'staff_id' => $this->staff_id,
            'preferred_locale' => $this->preferred_locale,
            'is_active' => $this->is_active,
            'last_login_at' => $this->last_login_at?->toIso8601String(),
            'failed_attempts' => $this->failed_attempts,
            'locked_until' => $this->locked_until,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
