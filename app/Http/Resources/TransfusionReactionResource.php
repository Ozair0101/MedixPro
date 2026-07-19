<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `transfusion_reaction`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class TransfusionReactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'transfusion_id' => $this->transfusion_id,
            'reaction_type' => $this->reaction_type,
            'severity' => $this->severity,
            'onset_at' => $this->onset_at?->toIso8601String(),
            'signs_symptoms' => $this->signs_symptoms,
            'action_taken' => $this->action_taken,
            'reported_by' => $this->reported_by,
            'investigated' => $this->investigated,
            'investigation_outcome' => $this->investigation_outcome,
        ];
    }
}
