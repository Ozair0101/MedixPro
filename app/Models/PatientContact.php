<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use App\Support\TextNormalizer;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Phone numbers and email addresses.
 *
 * `phone_belongs_to` is required for phones and is not bureaucratic detail:
 * shared and mahram phones are the norm, so the number on a patient record
 * frequently reaches someone else. Anything that sends to it — appointment
 * reminders, results notifications — has to know whose phone it is before
 * deciding what may be said (ADR-010).
 */
class PatientContact extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'patient_contact';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'patient_id', 'contact_type', 'value',
        'phone_belongs_to', 'sms_consent', 'is_primary', 'voided',
    ];

    protected $casts = [
        'sms_consent' => 'boolean',
        'is_primary' => 'boolean',
        'voided' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $contact) {
            $contact->value = $contact->contact_type === 'email'
                ? mb_strtolower(TextNormalizer::normalizeInput($contact->value) ?? '')
                : TextNormalizer::phone($contact->value);
        });
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * May clinical content be sent to this number?
     *
     * Consent alone is not sufficient: if the phone belongs to someone else,
     * the message must carry no name and no clinical detail.
     */
    public function mayReceiveClinicalContent(): bool
    {
        return $this->sms_consent && $this->phone_belongs_to === 'self';
    }
}
