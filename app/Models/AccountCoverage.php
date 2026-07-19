<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `account_coverage` table.
 */
class AccountCoverage extends Model
{
    protected $table = 'account_coverage';

    protected $primaryKey = 'account_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'coverage_id', 'priority',
    ];

    protected $casts = [
        'priority' => 'integer',
    ];
}
