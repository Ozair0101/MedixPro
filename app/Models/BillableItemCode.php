<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `billable_item_code` table.
 */
class BillableItemCode extends Model
{
    use HasUuids;

    protected $table = 'billable_item_code';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'item_id', 'code_system', 'code', 'valid_at',
    ];
}
