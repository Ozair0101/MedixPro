<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\PatientCompanion;
use App\Models\PatientContact;
use App\Models\PatientIdentifier;
use App\Models\Person;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Registers a patient: person, patient, identifiers, contacts, companions.
 *
 * All in one transaction — a person row without its patient row is an orphan
 * that no screen can reach and no MRN resolves to.
 */
class PatientRegistrationService
{
    public function __construct(private readonly PatientMatcher $matcher) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function register(array $data): Patient
    {
        return DB::transaction(function () use ($data) {
            // Score duplicates BEFORE the insert. Running it afterwards makes
            // the new record match itself — which the distinct_pair constraint
            // rejects, and which would be meaningless anyway.
            $candidates = $this->matcher->findDuplicates($data);

            $person = Person::create([
                // name_local is stored verbatim; only the derived search column
                // is normalized (ADR-005).
                'name_local' => $data['name_local'],
                'name_latin' => $data['name_latin'] ?? null,
                'given_name' => $data['given_name'],
                'father_name' => $data['father_name'] ?? null,
                'grandfather_name' => $data['grandfather_name'] ?? null,
                'family_or_tribal_name' => $data['family_or_tribal_name'] ?? null,
                'honorifics' => $data['honorifics'] ?? [],
                'gender' => $data['gender'],
                'birth_date' => $data['birth_date'] ?? null,
                'birth_date_precision' => $data['birth_date_precision'] ?? 'day',
                'birth_date_estimated' => $data['birth_date_estimated'] ?? false,
                'approximate_age_years' => $data['approximate_age_years'] ?? null,
                'marital_status' => $data['marital_status'] ?? null,
                'province_pcode' => $data['province_pcode'] ?? null,
                'district_pcode' => $data['district_pcode'] ?? null,
                'village' => $data['village'] ?? null,
                // Landmark-descriptive addressing carries more routing value
                // than every structured field combined.
                'address_detail' => $data['address_detail'] ?? null,
                'created_by' => Auth::id(),
            ]);

            $patient = Patient::create([
                'id' => $person->id,
                'blood_group' => $data['blood_group'] ?? null,
                'allergy_status' => $data['allergy_status'] ?? 'unknown',
                'is_unidentified' => $data['is_unidentified'] ?? false,
                'mother_patient_id' => $data['mother_patient_id'] ?? null,
                'registered_by' => Auth::id(),
            ]);

            $this->attachIdentifiers($patient, $data['identifiers'] ?? []);
            $this->attachContacts($patient, $data['contacts'] ?? []);
            $this->attachCompanions($patient, $data['companions'] ?? []);

            // Surface likely duplicates into the review queue rather than
            // waiting for someone to notice the wrong chart later. Scored
            // above, before this record existed.
            if ($candidates->isNotEmpty()) {
                $this->matcher->recordCandidates($patient->id, $candidates);
            }

            AuditLogger::record(
                action: 'create',
                table: 'patient',
                recordId: $patient->id,
                patientId: $patient->id,
                newValues: ['mrn' => $patient->mrn, 'name_local' => $person->name_local],
            );

            // Re-read from the database: the privacy defaults for female
            // patients are applied by a BEFORE INSERT trigger, and column
            // defaults are filled server-side. Without this refresh the
            // returned model reports null for settings that are actually set,
            // and a caller checking hide_name_on_queue would wrongly conclude
            // the name may be displayed.
            $patient->refresh();

            return $patient->load('person', 'identifiers', 'contacts', 'companions');
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Patient $patient, array $data): Patient
    {
        return DB::transaction(function () use ($patient, $data) {
            $person = $patient->person;

            $person->fill(array_filter([
                'name_local' => $data['name_local'] ?? null,
                'name_latin' => $data['name_latin'] ?? null,
                'given_name' => $data['given_name'] ?? null,
                'father_name' => $data['father_name'] ?? null,
                'grandfather_name' => $data['grandfather_name'] ?? null,
                'family_or_tribal_name' => $data['family_or_tribal_name'] ?? null,
                'gender' => $data['gender'] ?? null,
                'birth_date' => $data['birth_date'] ?? null,
                'marital_status' => $data['marital_status'] ?? null,
                'province_pcode' => $data['province_pcode'] ?? null,
                'district_pcode' => $data['district_pcode'] ?? null,
                'village' => $data['village'] ?? null,
                'address_detail' => $data['address_detail'] ?? null,
            ], fn ($v) => $v !== null));

            $person->updated_by = Auth::id();
            $person->save();

            if (array_key_exists('blood_group', $data) || array_key_exists('allergy_status', $data)) {
                $patient->fill(array_filter([
                    'blood_group' => $data['blood_group'] ?? null,
                    'allergy_status' => $data['allergy_status'] ?? null,
                ], fn ($v) => $v !== null))->save();
            }

            return $patient->fresh(['person', 'identifiers', 'contacts', 'companions']);
        });
    }

    /**
     * @param  array<int, array<string, mixed>>  $identifiers
     */
    private function attachIdentifiers(Patient $patient, array $identifiers): void
    {
        foreach ($identifiers as $identifier) {
            if (empty($identifier['value'])) {
                continue;
            }

            PatientIdentifier::create([
                'patient_id' => $patient->id,
                'type_code' => $identifier['type_code'],
                'value' => $identifier['value'],
                'issuing_authority' => $identifier['issuing_authority'] ?? null,
                'is_preferred' => $identifier['is_preferred'] ?? false,
            ]);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $contacts
     */
    private function attachContacts(Patient $patient, array $contacts): void
    {
        foreach ($contacts as $contact) {
            if (empty($contact['value'])) {
                continue;
            }

            PatientContact::create([
                'patient_id' => $patient->id,
                'contact_type' => $contact['contact_type'] ?? 'mobile',
                'value' => $contact['value'],
                // Who owns the phone determines what may ever be sent to it.
                'phone_belongs_to' => $contact['phone_belongs_to'] ?? null,
                'sms_consent' => $contact['sms_consent'] ?? false,
                'is_primary' => $contact['is_primary'] ?? false,
            ]);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $companions
     */
    private function attachCompanions(Patient $patient, array $companions): void
    {
        foreach ($companions as $companion) {
            if (empty($companion['name_local'])) {
                continue;
            }

            PatientCompanion::create([
                'patient_id' => $patient->id,
                'name_local' => $companion['name_local'],
                'relationship' => $companion['relationship'],
                'is_mahram' => $companion['is_mahram'] ?? false,
                'phone' => $companion['phone'] ?? null,
                'can_receive_results' => $companion['can_receive_results'] ?? false,
                'can_consent_on_behalf' => $companion['can_consent_on_behalf'] ?? false,
            ]);
        }
    }
}
