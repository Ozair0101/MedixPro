<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `gl_account` table.
 */
class GlAccount extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'gl_account';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'code', 'name_local', 'name_latin',
        'account_type', 'normal_side', 'parent_id', 'is_postable',
        'currency', 'must_not_go_negative', 'is_active',
    ];

    protected $casts = [
        'is_postable' => 'boolean',
        'must_not_go_negative' => 'boolean',
        'is_active' => 'boolean',
    ];
}
