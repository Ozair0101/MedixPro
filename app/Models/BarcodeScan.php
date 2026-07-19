<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `barcode_scan` table.
 */
class BarcodeScan extends Model
{
    use BelongsToFacility;

    protected $table = 'barcode_scan';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'raw_data', 'symbology', 'ai_01_gtin',
        'ai_10_lot', 'ai_17_expiry', 'ai_21_serial', 'resolved_lot_id',
        'scanned_at', 'scanned_by',
    ];

    protected $casts = [
        'ai_17_expiry' => 'date',
        'scanned_at' => 'datetime',
    ];
}
