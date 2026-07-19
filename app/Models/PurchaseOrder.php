<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `purchase_order` table.
 */
class PurchaseOrder extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'purchase_order';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'po_number', 'supplier_id', 'requisition_id',
        'status', 'order_date', 'expected_date', 'currency',
        'subtotal', 'tax_amount', 'total_amount', 'is_donation',
        'donor_name', 'notes', 'approved_by', 'approved_at',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_date' => 'date',
        'subtotal' => 'float',
        'tax_amount' => 'float',
        'total_amount' => 'float',
        'is_donation' => 'boolean',
        'approved_at' => 'datetime',
    ];
}
