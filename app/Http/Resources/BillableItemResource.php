<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `billable_item`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class BillableItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'item_code' => $this->item_code,
            'display_name_local' => $this->display_name_local,
            'display_name_latin' => $this->display_name_latin,
            'technical_name' => $this->technical_name,
            'category' => $this->category,
            'org_unit_id' => $this->org_unit_id,
            'default_unit' => $this->default_unit,
            'gl_revenue_account_id' => $this->gl_revenue_account_id,
            'tax_category' => $this->tax_category,
            'is_active' => $this->is_active,
        ];
    }
}
