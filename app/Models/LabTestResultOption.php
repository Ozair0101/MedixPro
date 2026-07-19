<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `lab_test_result_option` table.
 */
class LabTestResultOption extends Model
{
    use HasUuids;

    protected $table = 'lab_test_result_option';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'test_id', 'value_concept_id', 'value_text', 'is_normal',
        'sort_order',
    ];

    protected $casts = [
        'is_normal' => 'boolean',
        'sort_order' => 'integer',
    ];
}
