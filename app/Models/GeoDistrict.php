<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeoDistrict extends Model
{
    protected $table = 'geo_district';

    protected $primaryKey = 'pcode';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    public function province(): BelongsTo
    {
        return $this->belongsTo(GeoProvince::class, 'province_pcode', 'pcode');
    }
}
