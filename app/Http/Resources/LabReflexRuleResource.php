<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `lab_reflex_rule`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class LabReflexRuleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'trigger_test_id' => $this->trigger_test_id,
            'condition' => $this->condition,
            'reflex_test_id' => $this->reflex_test_id,
            'is_active' => $this->is_active,
        ];
    }
}
