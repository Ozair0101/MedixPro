<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Services\PatientMatcher;
use App\Services\PatientMergeService;
use App\Services\PatientRegistrationService;
use Database\Seeders\GeographySeeder;
use Database\Seeders\ReferenceDataSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class PatientRegistrationTest extends TestCase
{
    private string $facilityId;

    protected function setUp(): void
    {
        parent::setUp();

        if (DB::connection()->getDriverName() !== 'pgsql') {
            $this->markTestSkipped('Identity targets the PostgreSQL schema.');
        }

        DB::beginTransaction();

        $this->seed(GeographySeeder::class);
        $this->seed(ReferenceDataSeeder::class);

        $this->facilityId = (string) Str::uuid();

        DB::table('facility')->insert([
            'id' => $this->facilityId,
            'name_local' => 'شفاخانه تست',
            'name_latin' => 'Test Hospital',
            'facility_type' => 'district_hospital',
        ]);

        // Set the RLS context BEFORE touching any tenant-scoped table. The
        // `facility` table itself is not tenant-scoped (it defines the tenants),
        // but number_series is — and RLS will reject the insert without it.
        DB::statement('SELECT set_config(?, ?, true)', ['app.facility_id', $this->facilityId]);

        DB::table('number_series')->insert([
            'id' => (string) Str::uuid(),
            'facility_id' => $this->facilityId,
            'series_code' => 'MRN',
            'prefix' => 'MRN-',
            'padding' => 6,
            'next_value' => 1,
        ]);
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }

    private function service(): PatientRegistrationService
    {
        return app(PatientRegistrationService::class);
    }

    /** @param array<string, mixed> $overrides */
    private function register(array $overrides = []): Patient
    {
        return $this->service()->register(array_merge([
            'name_local' => 'احمد ولی',
            'given_name' => 'احمد',
            'father_name' => 'محمد',
            'gender' => 'M',
            'birth_date' => '1990-05-15',
        ], $overrides));
    }

    // -------------------------------------------------------------------
    // Registration
    // -------------------------------------------------------------------

    public function test_registration_creates_person_and_patient_with_an_mrn(): void
    {
        $patient = $this->register();

        $this->assertNotNull($patient->mrn);
        $this->assertStringStartsWith('MRN-', $patient->mrn);

        // Class-table inheritance: one identity, two rows.
        $this->assertSame($patient->id, $patient->person->id);
        $this->assertSame('احمد ولی', $patient->person->name_local);
    }

    public function test_the_stored_name_is_never_modified(): void
    {
        // name_local is authoritative and verbatim (ADR-005). Only the derived
        // search column is normalized.
        $awkward = 'حاجی  أحــمد ولي';

        $patient = $this->register(['name_local' => $awkward]);

        $this->assertSame($awkward, $patient->person->getRawOriginal('name_local'));
    }

    public function test_a_patient_with_only_a_given_name_can_register(): void
    {
        // A single given name is a complete legal name for most Afghans.
        // Requiring a surname would turn the registration desk away.
        $patient = $this->register([
            'name_local' => 'زرغونه',
            'given_name' => 'زرغونه',
            'father_name' => null,
        ]);

        $this->assertNotNull($patient->id);
    }

    public function test_a_patient_with_no_documents_and_no_birth_date_can_register(): void
    {
        // Under half the population holds an e-Tazkira, and many know only an
        // approximate age. Neither may gate care.
        $patient = $this->register([
            'birth_date' => null,
            'approximate_age_years' => 40,
            'identifiers' => [],
        ]);

        $this->assertNotNull($patient->id);
        $this->assertSame(40, $patient->person->ageYears());
    }

    public function test_a_female_patient_gets_protective_privacy_defaults(): void
    {
        $patient = $this->register(['gender' => 'F', 'name_local' => 'فاطمه']);

        $this->assertTrue($patient->hide_name_on_queue,
            'A female patient name must not appear on a public queue display.');
        $this->assertTrue($patient->prefers_same_gender_provider);
    }

    public function test_public_display_name_suppresses_a_female_patients_name(): void
    {
        $patient = $this->register(['gender' => 'F', 'name_local' => 'فاطمه'])
            ->load('person');

        $this->assertSame('فاطمه', $patient->person->displayName());
        $this->assertSame($patient->mrn, $patient->person->displayName(public: true));
    }

    public function test_allergy_status_distinguishes_unasked_from_none(): void
    {
        $unasked = $this->register();
        $this->assertTrue($unasked->allergiesNeverAssessed());

        $asked = $this->register([
            'name_local' => 'محمود',
            'given_name' => 'محمود',
            'allergy_status' => 'none_known',
        ]);
        $this->assertFalse($asked->allergiesNeverAssessed());
    }

    public function test_identifiers_and_contacts_are_normalized_on_save(): void
    {
        $patient = $this->register([
            'identifiers' => [[
                'type_code' => 'e_tazkira',
                // Persian digits, as an Afghan keyboard produces.
                'value' => '۱۲۳۴۵۶۷۸۹۰۱۲۳',
            ]],
            'contacts' => [[
                'contact_type' => 'mobile',
                'value' => '۰۷۰۱۲۳۴۵۶۷',
                'phone_belongs_to' => 'self',
            ]],
        ]);

        $this->assertSame('1234567890123', $patient->identifiers->first()->value,
            'Persian digits must fold to ASCII or lookups silently fail.');
        $this->assertSame('+93701234567', $patient->contacts->first()->value);
    }

    public function test_a_phone_belonging_to_someone_else_may_not_carry_clinical_content(): void
    {
        $patient = $this->register([
            'gender' => 'F',
            'contacts' => [[
                'contact_type' => 'mobile',
                'value' => '0701234567',
                'phone_belongs_to' => 'husband',
                'sms_consent' => true,
            ]],
        ]);

        $contact = $patient->contacts->first();

        $this->assertTrue($contact->sms_consent);
        $this->assertFalse($contact->mayReceiveClinicalContent(),
            'Consent is not enough: a shared or mahram phone reaches someone else.');
    }

    // -------------------------------------------------------------------
    // Search
    // -------------------------------------------------------------------

    public function test_an_arabic_keyboard_finds_a_patient_registered_on_an_afghan_one(): void
    {
        // THE case this whole normalization layer exists for. The two strings
        // are visually identical and differ only in codepoint (U+064A vs U+06CC).
        $this->register(['name_local' => 'احمد ولی', 'given_name' => 'احمد']);

        $results = app(PatientMatcher::class)->search('احمد ولي');

        $this->assertNotEmpty($results,
            'A clerk on an Arabic keyboard would otherwise get zero results and '
            .'create a duplicate record.');
    }

    public function test_search_by_mrn_accepts_persian_digits(): void
    {
        $patient = $this->register();

        $persian = strtr($patient->mrn, [
            '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴',
            '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹',
        ]);

        $results = app(PatientMatcher::class)->search($persian);

        $this->assertCount(1, $results);
        $this->assertSame($patient->id, $results->first()->id);
    }

    public function test_search_by_phone_number(): void
    {
        $patient = $this->register([
            'contacts' => [[
                'contact_type' => 'mobile',
                'value' => '0701234567',
                'phone_belongs_to' => 'self',
            ]],
        ]);

        $results = app(PatientMatcher::class)->search('0701234567');

        $this->assertTrue($results->contains('id', $patient->id));
    }

    public function test_search_by_national_id(): void
    {
        $patient = $this->register([
            'identifiers' => [['type_code' => 'e_tazkira', 'value' => '1234567890123']],
        ]);

        $results = app(PatientMatcher::class)->search('1234567890123');

        $this->assertTrue($results->contains('id', $patient->id));
    }

    // -------------------------------------------------------------------
    // Duplicate detection
    // -------------------------------------------------------------------

    public function test_a_near_identical_registration_is_flagged(): void
    {
        $this->register();

        $candidates = app(PatientMatcher::class)->findDuplicates([
            'name_local' => 'احمد ولی',
            'father_name' => 'محمد',
            'gender' => 'M',
            'birth_date' => '1990-05-15',
        ]);

        $this->assertNotEmpty($candidates);
        $this->assertSame('strong', $candidates->first()->confidence);
    }

    public function test_a_different_gender_is_never_proposed_as_a_duplicate(): void
    {
        $this->register(['gender' => 'M']);

        $candidates = app(PatientMatcher::class)->findDuplicates([
            'name_local' => 'احمد ولی',
            'father_name' => 'محمد',
            'gender' => 'F',
            'birth_date' => '1990-05-15',
        ]);

        $this->assertEmpty($candidates,
            'A false-positive merge across sexes fuses two unrelated histories.');
    }

    public function test_duplicates_are_queued_for_human_review_not_auto_merged(): void
    {
        $first = $this->register();
        $second = $this->register();

        // Both records still exist: nothing was merged automatically.
        $this->assertNotSame($first->id, $second->id);
        $this->assertTrue(Patient::find($first->id)->is_active);
        $this->assertTrue(Patient::find($second->id)->is_active);

        $this->assertGreaterThan(0,
            DB::table('patient_duplicate_candidate')->where('status', 'open')->count());
    }

    // -------------------------------------------------------------------
    // Merge
    // -------------------------------------------------------------------

    public function test_merge_deactivates_the_loser_and_resolves_forward(): void
    {
        $survivor = $this->register();
        $losing = $this->register(['name_local' => 'احمد ولي']);

        app(PatientMergeService::class)->merge($survivor->id, $losing->id, 'Same patient, duplicate registration.');

        // Never deleted — printed cards and referral letters still resolve.
        $reloaded = Patient::find($losing->id);
        $this->assertNotNull($reloaded);
        $this->assertFalse($reloaded->is_active);

        $this->assertSame($survivor->id, $reloaded->effective()->id);
    }

    public function test_merge_moves_child_rows_to_the_survivor(): void
    {
        $survivor = $this->register();
        $losing = $this->register([
            'name_local' => 'احمد ولي',
            'contacts' => [[
                'contact_type' => 'mobile', 'value' => '0700000001',
                'phone_belongs_to' => 'self',
            ]],
        ]);

        app(PatientMergeService::class)->merge($survivor->id, $losing->id, 'Duplicate.');

        $this->assertCount(1, Patient::find($survivor->id)->contacts,
            "The survivor must inherit the loser's contact details.");
    }

    public function test_merge_keeps_the_more_cautious_allergy_status(): void
    {
        $survivor = $this->register(['allergy_status' => 'unknown']);
        $losing = $this->register([
            'name_local' => 'احمد ولي',
            'allergy_status' => 'has_allergies',
        ]);

        app(PatientMergeService::class)->merge($survivor->id, $losing->id, 'Duplicate.');

        $this->assertSame('has_allergies', Patient::find($survivor->id)->allergy_status,
            'Losing a recorded allergy in a merge is the exact harm duplicates cause.');
    }

    public function test_a_merge_can_be_reversed(): void
    {
        $survivor = $this->register();
        $losing = $this->register([
            'name_local' => 'احمد ولي',
            'contacts' => [[
                'contact_type' => 'mobile', 'value' => '0700000002',
                'phone_belongs_to' => 'self',
            ]],
        ]);

        $link = app(PatientMergeService::class)
            ->merge($survivor->id, $losing->id, 'Assumed duplicate.');

        app(PatientMergeService::class)->unmerge($link->id, 'Different people after all.');

        $restored = Patient::find($losing->id);

        $this->assertTrue($restored->is_active);
        $this->assertSame($losing->id, $restored->effective()->id,
            'After unmerge the record must stand on its own again.');
        $this->assertCount(1, $restored->contacts,
            'Moved child rows must return to their original owner.');
    }

    public function test_a_merge_requires_a_reason(): void
    {
        $survivor = $this->register();
        $losing = $this->register(['name_local' => 'احمد ولي']);

        $this->expectException(\RuntimeException::class);
        app(PatientMergeService::class)->merge($survivor->id, $losing->id, '   ');
    }

    public function test_a_record_cannot_be_merged_twice(): void
    {
        $a = $this->register();
        $b = $this->register(['name_local' => 'احمد ولي']);
        $c = $this->register(['name_local' => 'احمد ولى']);

        app(PatientMergeService::class)->merge($a->id, $b->id, 'Duplicate.');

        $this->expectException(\RuntimeException::class);
        app(PatientMergeService::class)->merge($c->id, $b->id, 'Duplicate again.');
    }
}
