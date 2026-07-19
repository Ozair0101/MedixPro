<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `purchase_requisition` table.
 */
class PurchaseRequisition extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'purchase_requisition';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'requisition_number', 'requested_by', 'org_unit_id',
        'status', 'justification', 'requested_at', 'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
    ];
}
