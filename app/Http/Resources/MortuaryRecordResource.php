<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API representation of `mortuary_record`.
 *
 * facility_id is omitted: it is tenancy state derived from the
 * authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class MortuaryRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'deceased_name' => $this->deceased_name,
            'received_at' => $this->received_at?->toIso8601String(),
            'storage_unit' => $this->storage_unit,
            'death_time' => $this->death_time,
            'cause_of_death' => $this->cause_of_death,
            'is_medico_legal' => $this->is_medico_legal,
            'released_at' => $this->released_at?->toIso8601String(),
            'released_to_name' => $this->released_to_name,
            'released_to_relation' => $this->released_to_relation,
            'released_to_id_number' => $this->released_to_id_number,
            'authorized_by' => $this->authorized_by,
            'death_certificate_no' => $this->death_certificate_no,
        ];
    }
}
