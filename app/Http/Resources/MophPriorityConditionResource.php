<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `moph_priority_condition`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class MophPriorityConditionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name_local' => $this->name_local,
            'name_latin' => $this->name_latin,
            'form' => $this->form,
            'form_section' => $this->form_section,
            'sort_order' => $this->sort_order,
            'new_case_interval_days' => $this->new_case_interval_days,
            'uses_family_planning_rules' => $this->uses_family_planning_rules,
            'is_notifiable' => $this->is_notifiable,
            'valid_period' => $this->valid_period,
        ];
    }
}
