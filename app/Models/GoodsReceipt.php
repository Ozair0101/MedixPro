<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `goods_receipt` table.
 */
class GoodsReceipt extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'goods_receipt';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'grn_number', 'purchase_order_id', 'supplier_id',
        'location_id', 'received_at', 'received_by', 'supplier_invoice_no',
        'supplier_invoice_date', 'match_status', 'discrepancy_notes',
    ];

    protected $casts = [
        'received_at' => 'datetime',
        'supplier_invoice_date' => 'date',
    ];
}
