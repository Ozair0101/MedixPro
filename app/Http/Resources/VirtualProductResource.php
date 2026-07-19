<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `virtual_product`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class VirtualProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vtm_id' => $this->vtm_id,
            'name_latin' => $this->name_latin,
            'name_local' => $this->name_local,
            'dose_form_id' => $this->dose_form_id,
            'is_combination' => $this->is_combination,
            'prescribable' => $this->prescribable,
            'is_controlled' => $this->is_controlled,
            'controlled_schedule' => $this->controlled_schedule,
            'is_essential_medicine' => $this->is_essential_medicine,
            'is_high_alert' => $this->is_high_alert,
            'retired' => $this->retired,
        ];
    }
}
