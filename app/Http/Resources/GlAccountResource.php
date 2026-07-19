<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `gl_account`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class GlAccountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name_local' => $this->name_local,
            'name_latin' => $this->name_latin,
            'account_type' => $this->account_type,
            'normal_side' => $this->normal_side,
            'parent_id' => $this->parent_id,
            'is_postable' => $this->is_postable,
            'currency' => $this->currency,
            'must_not_go_negative' => $this->must_not_go_negative,
            'is_active' => $this->is_active,
        ];
    }
}
