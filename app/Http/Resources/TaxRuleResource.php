<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `tax_rule`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class TaxRuleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tax_type' => $this->tax_type,
            'tax_category' => $this->tax_category,
            'rate' => $this->rate,
            'is_credit_eligible' => $this->is_credit_eligible,
            'legal_citation' => $this->legal_citation,
            'valid_at' => $this->valid_at?->toIso8601String(),
        ];
    }
}
