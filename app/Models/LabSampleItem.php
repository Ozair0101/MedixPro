<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `lab_sample_item` table.
 */
class LabSampleItem extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'lab_sample_item';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'sample_id', 'parent_item_id', 'specimen_type_concept_id',
        'container_type', 'quantity', 'quantity_unit', 'collected_at',
        'collected_by', 'collection_method', 'collection_conditions', 'temperature_c',
        'received_at', 'rejected', 'rejection_reason', 'barcode',
    ];

    protected $casts = [
        'quantity' => 'float',
        'collected_at' => 'datetime',
        'temperature_c' => 'float',
        'received_at' => 'datetime',
        'rejected' => 'boolean',
    ];
}
