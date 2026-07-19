<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `invoice_line` table.
 */
class InvoiceLine extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'invoice_line';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'invoice_id', 'sequence', 'charge_item_id',
        'description', 'quantity', 'unit_price', 'discount_amount',
        'tax_amount', 'tax_rate', 'net_amount',
    ];

    protected $casts = [
        'sequence' => 'integer',
        'quantity' => 'float',
        'unit_price' => 'float',
        'discount_amount' => 'float',
        'tax_amount' => 'float',
        'tax_rate' => 'float',
        'net_amount' => 'float',
    ];
}
