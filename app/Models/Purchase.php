<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'reference_no',
        'status',
        'order_date',
        'expected_date',
        'total_amount',
        'currency',
        'received_by',
        'received_at',
        'hospital_id',
        'notes',
        'created_by',
    ];
}
