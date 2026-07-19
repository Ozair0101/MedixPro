<?php

namespace App\Models;

use App\Casts\PostgresArray;
use App\Models\Concerns\BelongsToFacility;
use App\Support\TextNormalizer;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * A human the system refers to: patient, relative, or member of staff.
 *
 * The person/patient split costs one join and buys three things a flat patient
 * table cannot do — a nurse who is also a patient, a retained former name, and
 * two MRNs from two branches.
 *
 * NAMES (ADR-005): name_local is authoritative and verbatim. It is never
 * auto-split, auto-capitalised or auto-corrected. Most Afghans have no surname;
 * `Mohammad` and `Abdul` are almost never standalone given names, so splitting
 * on whitespace would file half the Pashtun population under a handful of
 * duplicate "Mohammad" records. Only given_name is required.
 */
class Person extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'person';

    protected $fillable = [
        'facility_id',
        'name_local', 'name_latin', 'given_name', 'father_name',
        'grandfather_name', 'family_or_tribal_name', 'honorifics',
        'gender', 'birth_date', 'birth_date_precision', 'birth_date_estimated',
        'approximate_age_years', 'marital_status',
        'is_deceased', 'deceased_at', 'cause_of_death',
        'province_pcode', 'district_pcode', 'village', 'address_detail',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'birth_date_estimated' => 'boolean',
        'is_deceased' => 'boolean',
        'deceased_at' => 'datetime',
        'voided' => 'boolean',
        // Native text[], not JSON: Laravel's `array` cast writes `[]`, which
        // Postgres rejects as an array literal.
        'honorifics' => PostgresArray::class,
    ];

    /**
     * Keep the derived search columns in step with the stored name.
     *
     * The search key folds visually-identical letters so a clerk on an Arabic
     * keyboard finds a patient registered on an Afghan one — احمد ولي must
     * match احمد ولی. Crucially it is written to name_search ONLY; the value
     * the user typed is never modified (ADR-007).
     */
    protected static function booted(): void
    {
        static::saving(function (self $person) {
            $parts = array_filter([
                $person->name_local,
                $person->name_latin,
                $person->father_name,
                $person->grandfather_name,
                $person->family_or_tribal_name,
            ]);

            $person->name_search = TextNormalizer::searchKey(implode(' ', $parts));
            $person->name_soundex = $person->name_latin
                ? soundex($person->name_latin)
                : null;
        });
    }

    public function patient(): HasOne
    {
        return $this->hasOne(Patient::class, 'id');
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(GeoProvince::class, 'province_pcode', 'pcode');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(GeoDistrict::class, 'district_pcode', 'pcode');
    }

    /**
     * Best-effort age in years at a given date.
     *
     * Returns null rather than guessing when neither a birth date nor an
     * approximate age was recorded — a fabricated age is worse than a blank one
     * in a paediatric dosing calculation.
     */
    public function ageYears(?\DateTimeInterface $at = null): ?int
    {
        if ($this->birth_date) {
            return $this->birth_date->diffInYears($at ?? now());
        }

        return $this->approximate_age_years;
    }

    /**
     * MoPH disaggregation band. Every routine indicator is reported as
     * under-5 M/F and over-5 M/F, computed at SERVICE DATE — not today.
     */
    public function ageGroup(?\DateTimeInterface $at = null): ?string
    {
        $age = $this->ageYears($at);

        return $age === null ? null : ($age < 5 ? 'under5' : 'over5');
    }

    /**
     * Display name honouring the privacy policy.
     *
     * Public-facing surfaces (queue screens, wristbands, SMS) must not show a
     * female patient's name (ADR-010). Callers pass $public = true for anything
     * visible beyond the consulting room.
     */
    public function displayName(bool $public = false): string
    {
        if ($public && $this->gender === 'F') {
            return $this->patient?->mrn ?? '—';
        }

        return $this->name_local;
    }
}
