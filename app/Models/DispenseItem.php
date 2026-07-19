<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One lot's worth of a dispense.
 *
 * A single prescription line routinely spans several lots — FEFO takes what is
 * left of the oldest before moving on — so the lot lives here rather than on
 * the dispense header. Carrying the lot per line is also what makes a recall
 * answerable: "who received anything from lot X" is one query.
 */
class DispenseItem extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'dispense_item';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'dispense_id', 'stock_item_id', 'stock_lot_id',
        'quantity', 'unit_id', 'base_quantity', 'unit_price', 'ledger_id',
    ];

    protected $casts = [
        'quantity' => 'float',
        'base_quantity' => 'float',
    ];

    public function dispense(): BelongsTo
    {
        return $this->belongsTo(Dispense::class);
    }
}
