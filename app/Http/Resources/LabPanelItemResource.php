<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `lab_panel_item`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class LabPanelItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'panel_id' => $this->panel_id,
            'test_id' => $this->test_id,
            'sort_order' => $this->sort_order,
        ];
    }
}
