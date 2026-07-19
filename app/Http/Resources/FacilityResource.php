<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `facility`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class FacilityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'moph_facility_code' => $this->moph_facility_code,
            'name_local' => $this->name_local,
            'name_latin' => $this->name_latin,
            'facility_type' => $this->facility_type,
            'moph_form_type' => $this->moph_form_type,
            'province_pcode' => $this->province_pcode,
            'district_pcode' => $this->district_pcode,
            'address_detail' => $this->address_detail,
            'phone' => $this->phone,
            'licensed_beds' => $this->licensed_beds,
            'legal_name_local' => $this->legal_name_local,
            'tin' => $this->tin,
            'business_licence_no' => $this->business_licence_no,
            'tax_exempt' => $this->tax_exempt,
            'tax_exemption_ref' => $this->tax_exemption_ref,
            'tax_exemption_from' => $this->tax_exemption_from,
            'tax_exemption_to' => $this->tax_exemption_to,
            'electricity_source' => $this->electricity_source,
            'electricity_hours_per_day' => $this->electricity_hours_per_day,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
