<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Patient representation for the API.
 *
 * Deliberately omits facility_id: it is server-side tenancy state derived from
 * the authenticated user, and echoing it back invites clients to treat it as
 * something they may set.
 */
class PatientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $person = $this->person;

        return [
            'id' => $this->id,
            'mrn' => $this->mrn,

            'name' => [
                // Authoritative and verbatim — never reconstructed from parts.
                'local' => $person?->name_local,
                'latin' => $person?->name_latin,
                'given' => $person?->given_name,
                'father' => $person?->father_name,
                'grandfather' => $person?->grandfather_name,
                'family_or_tribal' => $person?->family_or_tribal_name,
                'honorifics' => $person?->honorifics ?? [],
            ],

            'gender' => $person?->gender,
            'birth_date' => $person?->birth_date?->toDateString(),
            'birth_date_precision' => $person?->birth_date_precision,
            'birth_date_estimated' => (bool) $person?->birth_date_estimated,
            'approximate_age_years' => $person?->approximate_age_years,
            'age_years' => $person?->ageYears(),
            'marital_status' => $person?->marital_status,

            'blood_group' => $this->blood_group,
            'allergy_status' => $this->allergy_status,
            // "Nobody has asked yet" must be visibly different from "asked, none".
            'allergies_never_assessed' => $this->allergiesNeverAssessed(),

            'address' => [
                'province_pcode' => $person?->province_pcode,
                'district_pcode' => $person?->district_pcode,
                'village' => $person?->village,
                'detail' => $person?->address_detail,
            ],

            'privacy' => [
                'hide_name_on_queue' => (bool) $this->hide_name_on_queue,
                'hide_name_on_wristband' => (bool) $this->hide_name_on_wristband,
                'prefers_same_gender_provider' => (bool) $this->prefers_same_gender_provider,
                'sms_consent' => (bool) $this->sms_consent,
            ],

            'is_active' => (bool) $this->is_active,
            'is_unidentified' => (bool) $this->is_unidentified,

            'identifiers' => $this->whenLoaded('identifiers', fn () => $this->identifiers->map(fn ($i) => [
                'id' => $i->id,
                'type_code' => $i->type_code,
                'value' => $i->value,
                'is_preferred' => (bool) $i->is_preferred,
            ])),

            'contacts' => $this->whenLoaded('contacts', fn () => $this->contacts->map(fn ($c) => [
                'id' => $c->id,
                'contact_type' => $c->contact_type,
                'value' => $c->value,
                'phone_belongs_to' => $c->phone_belongs_to,
                'sms_consent' => (bool) $c->sms_consent,
                'is_primary' => (bool) $c->is_primary,
                // Exposed so the UI never offers to text clinical detail to a
                // number that belongs to someone else.
                'may_receive_clinical_content' => $c->mayReceiveClinicalContent(),
            ])),

            'companions' => $this->whenLoaded('companions', fn () => $this->companions->map(fn ($c) => [
                'id' => $c->id,
                'name_local' => $c->name_local,
                'relationship' => $c->relationship,
                'is_mahram' => (bool) $c->is_mahram,
                'phone' => $c->phone,
                'can_receive_results' => (bool) $c->can_receive_results,
                'can_consent_on_behalf' => (bool) $c->can_consent_on_behalf,
            ])),

            'registered_at' => $this->registered_at?->toIso8601String(),
        ];
    }
}
