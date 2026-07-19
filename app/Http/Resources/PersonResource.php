<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `person`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class PersonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name_local' => $this->name_local,
            'name_latin' => $this->name_latin,
            'given_name' => $this->given_name,
            'father_name' => $this->father_name,
            'grandfather_name' => $this->grandfather_name,
            'family_or_tribal_name' => $this->family_or_tribal_name,
            'honorifics' => $this->honorifics,
            'name_search' => $this->name_search,
            'name_soundex' => $this->name_soundex,
            'gender' => $this->gender,
            'birth_date' => $this->birth_date,
            'birth_date_precision' => $this->birth_date_precision,
            'birth_date_estimated' => $this->birth_date_estimated,
            'approximate_age_years' => $this->approximate_age_years,
            'marital_status' => $this->marital_status,
            'is_deceased' => $this->is_deceased,
            'deceased_at' => $this->deceased_at?->toIso8601String(),
            'cause_of_death' => $this->cause_of_death,
            'province_pcode' => $this->province_pcode,
            'district_pcode' => $this->district_pcode,
            'village' => $this->village,
            'address_detail' => $this->address_detail,
            'created_at' => $this->created_at?->toIso8601String(),
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at?->toIso8601String(),
            'updated_by' => $this->updated_by,
            'voided' => $this->voided,
            'voided_by' => $this->voided_by,
            'voided_at' => $this->voided_at?->toIso8601String(),
            'void_reason' => $this->void_reason,
        ];
    }
}
