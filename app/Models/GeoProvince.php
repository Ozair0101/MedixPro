<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GeoProvince extends Model
{
    protected $table = 'geo_province';

    protected $primaryKey = 'pcode';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    public function districts(): HasMany
    {
        return $this->hasMany(GeoDistrict::class, 'province_pcode', 'pcode');
    }
}
