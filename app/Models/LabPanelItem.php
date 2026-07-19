<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `lab_panel_item` table.
 */
class LabPanelItem extends Model
{
    protected $table = 'lab_panel_item';

    protected $primaryKey = 'panel_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'test_id', 'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];
}
