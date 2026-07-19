<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `cashier_shift`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class CashierShiftResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cash_point_id' => $this->cash_point_id,
            'cashier_id' => $this->cashier_id,
            'opened_at' => $this->opened_at?->toIso8601String(),
            'opening_float' => $this->opening_float,
            'closed_at' => $this->closed_at?->toIso8601String(),
            'declared_cash' => $this->declared_cash,
            'expected_cash' => $this->expected_cash,
            'variance' => $this->variance,
            'variance_reason' => $this->variance_reason,
            'closed_by' => $this->closed_by,
            'status' => $this->status,
        ];
    }
}
