<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'medication_id',
        'category_id',
        'batch_no',
        'expiry_date',
        'unit_cost',
        'quantity_ordered',
        'quantity_received',
        'tax',
        'discount',
        'line_total',
        'hospital_id',
    ];
}
