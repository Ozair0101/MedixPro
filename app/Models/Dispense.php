<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dispense extends Model
{
    use HasFactory;

    protected $fillable = [
        'prescription_item_id',
        'medication_id',
        'batch_id',
        'quantity',
        'pharmacist_id',
        'dispense_time',
        'hospital_id',
    ];

    protected $casts = [
        'dispense_time' => 'datetime',
    ];

    public function prescriptionItem(): BelongsTo
    {
        return $this->belongsTo(PrescriptionItem::class);
    }

    public function medication(): BelongsTo
    {
        return $this->belongsTo(Medication::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(MedicationBatch::class, 'batch_id');
    }

    public function scopeForHospital($query, int $hospitalId)
    {
        return $query->where('hospital_id', $hospitalId);
    }
}
