<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `moph_report_line` table.
 */
class MophReportLine extends Model
{
    use HasUuids;

    protected $table = 'moph_report_line';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'report_id', 'section', 'row_code', 'row_label',
        'under5_male', 'under5_female', 'over5_male', 'over5_female',
        'numeric_value', 'text_value', 'manual_override', 'override_reason',
    ];

    protected $casts = [
        'under5_male' => 'integer',
        'under5_female' => 'integer',
        'over5_male' => 'integer',
        'over5_female' => 'integer',
        'numeric_value' => 'float',
        'manual_override' => 'boolean',
    ];
}
