<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `lab_qc_run`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class LabQcRunResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'test_id' => $this->test_id,
            'control_lot' => $this->control_lot,
            'control_level' => $this->control_level,
            'expected_value' => $this->expected_value,
            'expected_sd' => $this->expected_sd,
            'observed_value' => $this->observed_value,
            'is_in_control' => $this->is_in_control,
            'westgard_rule_violated' => $this->westgard_rule_violated,
            'run_at' => $this->run_at?->toIso8601String(),
            'run_by' => $this->run_by,
            'action_taken' => $this->action_taken,
        ];
    }
}
