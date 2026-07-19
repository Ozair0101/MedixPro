<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `barcode_scan`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class BarcodeScanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'raw_data' => $this->raw_data,
            'symbology' => $this->symbology,
            'ai_01_gtin' => $this->ai_01_gtin,
            'ai_10_lot' => $this->ai_10_lot,
            'ai_17_expiry' => $this->ai_17_expiry,
            'ai_21_serial' => $this->ai_21_serial,
            'resolved_lot_id' => $this->resolved_lot_id,
            'scanned_at' => $this->scanned_at?->toIso8601String(),
            'scanned_by' => $this->scanned_by,
        ];
    }
}
