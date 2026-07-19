<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use App\Support\TextNormalizer;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * External identifiers: e-Tazkira, paper tazkira, passport, UNHCR registration.
 *
 * A child table rather than columns on `patient`, because a patient may hold
 * several, may hold none, and — importantly — may ACQUIRE an e-Tazkira later.
 * Widening the patient table cannot express that history.
 *
 * The stored `value` is digit-normalized; `value_raw` keeps exactly what was
 * presented, so a card that reads oddly can still be reconciled by hand.
 */
class PatientIdentifier extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'patient_identifier';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'patient_id', 'type_code', 'value', 'value_raw',
        'issuing_authority', 'issued_on', 'expires_on', 'is_preferred', 'voided',
    ];

    protected $casts = [
        'issued_on' => 'date',
        'expires_on' => 'date',
        'is_preferred' => 'boolean',
        'voided' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $identifier) {
            $identifier->value_raw ??= $identifier->value;

            $type = IdentifierType::find($identifier->type_code);

            // Numeric identifiers are stored digits-only, so a value typed with
            // Persian digits or separators still matches on lookup.
            $identifier->value = $type?->normalize_digits
                ? TextNormalizer::digitsOnly($identifier->value)
                : TextNormalizer::normalizeInput($identifier->value);
        });
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(IdentifierType::class, 'type_code', 'code');
    }
}
