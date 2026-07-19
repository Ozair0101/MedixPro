<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use App\Support\MedicalRecordNumber;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * A person who receives care.
 *
 * The primary key IS person.id — class-table inheritance, so a person can gain
 * the patient role without changing identity, and a staff member who becomes a
 * patient keeps one record.
 */
class Patient extends Model
{
    use BelongsToFacility;

    protected $table = 'patient';

    public $incrementing = false;

    protected $keyType = 'string';

    // The schema uses registered_at rather than Laravel's created_at/updated_at:
    // when a patient was registered is domain data, not row metadata.
    public $timestamps = false;

    protected $fillable = [
        'id', 'facility_id', 'mrn', 'blood_group', 'allergy_status',
        'is_unidentified', 'mother_patient_id',
        'hide_name_on_wristband', 'hide_name_on_queue', 'sms_consent',
        'prefers_same_gender_provider', 'registered_by', 'is_active',
    ];

    protected $casts = [
        'is_unidentified' => 'boolean',
        'hide_name_on_wristband' => 'boolean',
        'hide_name_on_queue' => 'boolean',
        'sms_consent' => 'boolean',
        'prefers_same_gender_provider' => 'boolean',
        'is_active' => 'boolean',
        'registered_at' => 'datetime',
    ];

    /**
     * Allocate an MRN when one was not supplied.
     *
     * Drawn from the row-locked number_series table, replacing the previous
     * generate-random-then-check-uniqueness loop: that pattern is a race, and
     * two clerks registering simultaneously could be handed the same code.
     *
     * Never derived from a national ID — under half the population holds an
     * e-Tazkira, so an MRN that depended on one would leave most patients
     * unregisterable (ADR-006).
     */
    protected static function booted(): void
    {
        static::creating(function (self $patient) {
            if (empty($patient->mrn)) {
                $patient->mrn = MedicalRecordNumber::next(
                    $patient->facility_id ?? static::currentFacilityId()
                );
            }
        });
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'id');
    }

    public function identifiers(): HasMany
    {
        return $this->hasMany(PatientIdentifier::class)->where('voided', false);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(PatientContact::class)->where('voided', false);
    }

    public function companions(): HasMany
    {
        return $this->hasMany(PatientCompanion::class)->where('is_active', true);
    }

    public function mother(): BelongsTo
    {
        return $this->belongsTo(self::class, 'mother_patient_id');
    }

    public function newborns(): HasMany
    {
        return $this->hasMany(self::class, 'mother_patient_id');
    }

    /** The link that supersedes this record, if it lost a merge. */
    public function replacedBy(): HasOne
    {
        return $this->hasOne(PatientLink::class, 'patient_id')
            ->where('link_type', 'replaced_by')
            ->whereNull('reversed_at');
    }

    /**
     * Follow merge links to the surviving record.
     *
     * Merging never deletes: the loser is deactivated and linked, so external
     * systems and printed cards holding the old MRN keep resolving.
     */
    public function effective(): self
    {
        $seen = [$this->id];
        $current = $this;

        while ($link = $current->replacedBy) {
            if (in_array($link->other_patient_id, $seen, true)) {
                break;   // defensive: a merge cycle must not hang a request
            }

            $next = self::find($link->other_patient_id);

            if (! $next) {
                break;
            }

            $seen[] = $next->id;
            $current = $next;
        }

        return $current;
    }

    public function hasKnownAllergies(): bool
    {
        return $this->allergy_status === 'has_allergies';
    }

    /**
     * "Nobody has asked yet" is clinically different from "we asked and there
     * are none". An empty allergy table cannot express that difference, which
     * is why allergy_status exists alongside it.
     */
    public function allergiesNeverAssessed(): bool
    {
        return $this->allergy_status === 'unknown';
    }
}
