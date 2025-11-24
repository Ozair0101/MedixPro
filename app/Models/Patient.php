<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'blood_type',
        'marital_status',
        'email',
        'phone_number',
        'emergency_contact_phone',
        'emergency_name',
        'medical_file',
        'remark',
        'address_json',
        'is_active',
        'city',
        'street_address'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
        'address_json' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) \Illuminate\Support\Str::uuid();
            }

            if (empty($model->patient_code)) {
                $model->patient_code = static::generatePatientCode();
            }
        });
    }

    protected static function generatePatientCode(): string
    {
        do {
            $code = 'MID-' . str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (static::where('patient_code', $code)->exists());

        return $code;
    }
}
