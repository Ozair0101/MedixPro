<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `hand_hygiene_audit`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class HandHygieneAuditResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'org_unit_id' => $this->org_unit_id,
            'audit_date' => $this->audit_date,
            'opportunities' => $this->opportunities,
            'compliant' => $this->compliant,
            'compliance_rate' => $this->compliance_rate,
            'observed_by' => $this->observed_by,
        ];
    }
}
