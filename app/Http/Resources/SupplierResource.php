<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `supplier`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class SupplierResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'contact_person' => $this->contact_person,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'tin' => $this->tin,
            'payment_terms' => $this->payment_terms,
            'has_business_licence' => $this->has_business_licence,
            'withholding_rate' => $this->withholding_rate,
            'rating' => $this->rating,
            'is_active' => $this->is_active,
        ];
    }
}
