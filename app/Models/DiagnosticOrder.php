<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `diagnostic_order` table.
 */
class DiagnosticOrder extends Model
{
    use HasUuids;

    protected $table = 'diagnostic_order';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'specimen_type_concept_id', 'body_site_concept_id', 'laterality', 'clinical_history',
        'reflex_from_order_id',
    ];
}
