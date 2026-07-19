<?php

namespace App\Http\Requests;

use App\Models\IdentifierType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for patient registration.
 *
 * The guiding principle: VALIDATE FORMAT, NEVER GATE CARE. Every field that a
 * displaced, undocumented or unaccompanied patient might be unable to supply is
 * optional. A registration desk that cannot register someone is a clinical
 * failure, not a data-quality success.
 *
 * So: no required national ID, no required date of birth, no required
 * companion, no required surname, no required address.
 */
class RegisterPatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('patient.create') ?? true;
    }

    public function rules(): array
    {
        return [
            // ---- Name (ADR-005) --------------------------------------------
            // name_local is authoritative and stored verbatim. given_name is
            // the ONLY required component: a single given name is a complete
            // legal name for most Afghans.
            'name_local' => ['required', 'string', 'max:255'],
            'name_latin' => ['nullable', 'string', 'max:255'],
            'given_name' => ['required', 'string', 'max:100'],
            'father_name' => ['nullable', 'string', 'max:100'],
            'grandfather_name' => ['nullable', 'string', 'max:100'],
            'family_or_tribal_name' => ['nullable', 'string', 'max:100'],
            'honorifics' => ['nullable', 'array'],
            'honorifics.*' => ['string', 'max:50'],

            // ---- Demographics ----------------------------------------------
            'gender' => ['required', Rule::in(['M', 'F', 'U'])],

            // Either a birth date or an approximate age — never both required.
            // Many patients know only a Shamsi year, and some know neither.
            'birth_date' => ['nullable', 'date', 'before_or_equal:today',
                'required_without:approximate_age_years'],
            'birth_date_precision' => ['nullable', Rule::in(['day', 'month', 'year'])],
            'birth_date_estimated' => ['nullable', 'boolean'],
            'approximate_age_years' => ['nullable', 'integer', 'min:0', 'max:130',
                'required_without:birth_date'],

            'marital_status' => ['nullable',
                Rule::in(['single', 'married', 'widowed', 'divorced', 'unknown'])],
            'blood_group' => ['nullable',
                Rule::in(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])],
            'allergy_status' => ['nullable',
                Rule::in(['unknown', 'none_known', 'has_allergies'])],

            'is_unidentified' => ['nullable', 'boolean'],
            'mother_patient_id' => ['nullable', 'uuid', 'exists:patient,id'],

            // ---- Address ----------------------------------------------------
            'province_pcode' => ['nullable', 'string', 'size:4', 'exists:geo_province,pcode'],
            'district_pcode' => ['nullable', 'string', 'size:6', 'exists:geo_district,pcode'],
            // Free text by design: no canonical national village dataset exists.
            'village' => ['nullable', 'string', 'max:150'],
            // Afghan addressing is landmark-descriptive; this field routinely
            // carries more usable information than every structured field.
            'address_detail' => ['nullable', 'string', 'max:500'],

            // ---- Identifiers -------------------------------------------------
            'identifiers' => ['nullable', 'array'],
            'identifiers.*.type_code' => ['required_with:identifiers',
                'exists:identifier_type,code'],
            'identifiers.*.value' => ['required_with:identifiers', 'string', 'max:100'],
            'identifiers.*.issuing_authority' => ['nullable', 'string', 'max:150'],
            'identifiers.*.is_preferred' => ['nullable', 'boolean'],

            // ---- Contacts ----------------------------------------------------
            'contacts' => ['nullable', 'array'],
            'contacts.*.contact_type' => ['required_with:contacts',
                Rule::in(['mobile', 'landline', 'email'])],
            'contacts.*.value' => ['required_with:contacts', 'string', 'max:150'],
            // Required for phones: shared and mahram phones are the norm, and
            // anything that sends to the number must know whose it is.
            'contacts.*.phone_belongs_to' => ['nullable',
                Rule::in(['self', 'husband', 'father', 'son', 'brother', 'mother', 'other'])],
            'contacts.*.sms_consent' => ['nullable', 'boolean'],
            'contacts.*.is_primary' => ['nullable', 'boolean'],

            // ---- Companions (always optional) --------------------------------
            'companions' => ['nullable', 'array'],
            'companions.*.name_local' => ['required_with:companions', 'string', 'max:255'],
            'companions.*.relationship' => ['required_with:companions',
                Rule::in(['husband', 'father', 'brother', 'son', 'mother', 'sister',
                    'daughter', 'guardian', 'other'])],
            'companions.*.is_mahram' => ['nullable', 'boolean'],
            'companions.*.phone' => ['nullable', 'string', 'max:30'],
            'companions.*.can_receive_results' => ['nullable', 'boolean'],
            'companions.*.can_consent_on_behalf' => ['nullable', 'boolean'],

            // Set when the clerk has reviewed duplicate candidates and is
            // deliberately creating a new record anyway.
            'acknowledged_duplicates' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // A phone number without a recorded owner cannot be used safely.
            foreach ($this->input('contacts', []) as $i => $contact) {
                $isPhone = in_array($contact['contact_type'] ?? 'mobile',
                    ['mobile', 'landline'], true);

                if ($isPhone && empty($contact['phone_belongs_to'])) {
                    $validator->errors()->add(
                        "contacts.{$i}.phone_belongs_to",
                        'Record whose phone this is — shared phones are common, and '
                        .'we must know before sending anything to it.'
                    );
                }
            }

            // Format-only identifier validation, per identifier type.
            foreach ($this->input('identifiers', []) as $i => $identifier) {
                $type = IdentifierType::find($identifier['type_code'] ?? '');

                if (! $type?->validation_regex) {
                    continue;   // no authoritative format published; accept as typed
                }

                $normalized = preg_replace('/\D+/', '', (string) ($identifier['value'] ?? ''));

                if (! preg_match('/'.$type->validation_regex.'/', $normalized)) {
                    $validator->errors()->add(
                        "identifiers.{$i}.value",
                        "Does not match the expected format for {$type->name_latin}."
                    );
                }
            }
        });
    }

    public function attributes(): array
    {
        return [
            'name_local' => 'name',
            'given_name' => 'given name',
            'father_name' => "father's name",
        ];
    }
}
