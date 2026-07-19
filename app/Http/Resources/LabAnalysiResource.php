<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `lab_analysis`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class LabAnalysiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sample_item_id' => $this->sample_item_id,
            'test_id' => $this->test_id,
            'panel_id' => $this->panel_id,
            'order_id' => $this->order_id,
            'section_id' => $this->section_id,
            'status' => $this->status,
            'revision' => $this->revision,
            'parent_analysis_id' => $this->parent_analysis_id,
            'reflex_triggered' => $this->reflex_triggered,
            'analyzer_id' => $this->analyzer_id,
            'started_at' => $this->started_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'technical_accepted_by' => $this->technical_accepted_by,
            'technical_accepted_at' => $this->technical_accepted_at?->toIso8601String(),
            'clinically_validated_by' => $this->clinically_validated_by,
            'clinically_validated_at' => $this->clinically_validated_at?->toIso8601String(),
            'released_at' => $this->released_at?->toIso8601String(),
            'is_reportable' => $this->is_reportable,
            'referred_to_org' => $this->referred_to_org,
            'referred_at' => $this->referred_at?->toIso8601String(),
        ];
    }
}
