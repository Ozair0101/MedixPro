<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `moph_priority_condition` table.
 */
class MophPriorityCondition extends Model
{
    use HasUuids;

    protected $table = 'moph_priority_condition';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'code', 'name_local', 'name_latin', 'form',
        'form_section', 'sort_order', 'new_case_interval_days', 'uses_family_planning_rules',
        'is_notifiable', 'valid_period',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'new_case_interval_days' => 'integer',
        'uses_family_planning_rules' => 'boolean',
        'is_notifiable' => 'boolean',
    ];
}
