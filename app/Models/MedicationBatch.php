<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicationBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'medication_id',
        'batch_no',
        'expiry_date',
        'quantity',
        'cost_price',
        'selling_price',
        'hospital_id',
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    public function medication(): BelongsTo
    {
        return $this->belongsTo(Medication::class);
    }

    public function dispenses(): HasMany
    {
        return $this->hasMany(Dispense::class, 'batch_id');
    }

    public function scopeForHospital($query, int $hospitalId)
    {
        return $query->where('hospital_id', $hospitalId);
    }
}
