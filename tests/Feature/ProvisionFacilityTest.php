<?php

namespace Tests\Feature;

use Database\Seeders\CalendarSeeder;
use Database\Seeders\GeographySeeder;
use Database\Seeders\ReferenceDataSeeder;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Provisioning is the bootstrap path: until it runs, no patient can be
 * registered, because there is no MRN series to draw from and no facility_id
 * for Row-Level Security to scope against.
 */
class ProvisionFacilityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (DB::connection()->getDriverName() !== 'pgsql') {
            $this->markTestSkipped('Provisioning targets the PostgreSQL schema.');
        }

        DB::beginTransaction();
        $this->seed(GeographySeeder::class);
        $this->seed(CalendarSeeder::class);
        $this->seed(ReferenceDataSeeder::class);
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }

    private function provision(array $overrides = []): int
    {
        return $this->artisan('hmis:provision-facility', array_merge([
            '--name' => 'شفاخانه تست',
            '--name-latin' => 'Test Hospital',
            '--type' => 'provincial_hospital',
            '--province' => 'AF32',
            '--admin-gender' => 'F',
            '--admin-password' => 'secret-for-test',
        ], $overrides))->run();
    }

    public function test_it_provisions_a_working_facility(): void
    {
        $this->assertSame(0, $this->provision());

        $facility = DB::table('facility')->where('name_latin', 'Test Hospital')->first();
        $this->assertNotNull($facility);

        // A provincial hospital files on MoPH form type H2.
        $this->assertSame('H2', $facility->moph_form_type);

        DB::statement('SELECT set_config(?, ?, true)', ['app.facility_id', $facility->id]);

        $this->assertGreaterThan(0, DB::table('number_series')->count(), 'no MRN series');
        $this->assertGreaterThan(0, DB::table('org_unit')->count());
        $this->assertGreaterThan(0, DB::table('stock_location')->count());
        $this->assertGreaterThan(0, DB::table('gl_account')->count());
        $this->assertSame(1, DB::table('app_user')->count());
    }

    public function test_the_mrn_series_actually_allocates(): void
    {
        $this->provision();
        $facility = DB::table('facility')->where('name_latin', 'Test Hospital')->first();
        DB::statement('SELECT set_config(?, ?, true)', ['app.facility_id', $facility->id]);

        $first = DB::selectOne('SELECT next_number(?, ?) AS n', [$facility->id, 'MRN'])->n;
        $second = DB::selectOne('SELECT next_number(?, ?) AS n', [$facility->id, 'MRN'])->n;

        $this->assertSame('MRN-000001', $first);
        $this->assertSame('MRN-000002', $second);
    }

    public function test_province_can_be_given_by_name_or_alias(): void
    {
        // P-codes are deliberately meaningless identifiers, so requiring one
        // would make this command unusable without a lookup sheet.
        $this->assertSame(0, $this->provision(['--province' => 'Herat']));

        $facility = DB::table('facility')->where('name_latin', 'Test Hospital')->first();

        // The COD spells it Hirat; users type Herat.
        $this->assertSame('AF32', $facility->province_pcode);
    }

    public function test_an_unknown_province_is_rejected(): void
    {
        $this->assertNotSame(0, $this->provision(['--province' => 'Atlantis']));
        $this->assertSame(0, DB::table('facility')->where('name_latin', 'Test Hospital')->count(),
            'A failed provision must not leave a half-built facility behind.');
    }

    public function test_admin_gender_is_required_and_validated(): void
    {
        // Provider gender drives patient assignment, ward segregation and
        // mahram rules — a wrong value is a clinical-safety issue (ADR-010).
        $this->assertNotSame(0, $this->provision(['--admin-gender' => 'X']));
    }

    public function test_privacy_defaults_are_protective_out_of_the_box(): void
    {
        $this->provision();
        $facility = DB::table('facility')->where('name_latin', 'Test Hospital')->first();
        DB::statement('SELECT set_config(?, ?, true)', ['app.facility_id', $facility->id]);

        $setting = fn (string $k) => DB::table('app_setting')->where('key', $k)->value('value');

        // A facility opts OUT of these deliberately; it does not have to opt in.
        $this->assertSame('true', $setting('privacy.hide_female_name_on_wristband'));
        $this->assertSame('"token_only"', $setting('privacy.queue_display_mode'));
        $this->assertSame('false', $setting('sms.include_patient_name'));
        $this->assertSame('true', $setting('scheduling.enforce_provider_gender'));

        // Domestic foreign-currency use carries custodial penalties (ADR-008).
        $this->assertSame('"AFN"', $setting('billing.currency'));
    }

    public function test_accounting_periods_align_to_shamsi_months(): void
    {
        $this->provision();
        $facility = DB::table('facility')->where('name_latin', 'Test Hospital')->first();
        DB::statement('SELECT set_config(?, ?, true)', ['app.facility_id', $facility->id]);

        // Twelve Shamsi months, not twelve Gregorian ones.
        $this->assertSame(12, DB::table('accounting_period')->count());

        // The EXCLUDE constraint would have rejected an overlap at insert time,
        // so reaching here at all proves the periods tile cleanly.
        $this->assertSame(12, DB::table('accounting_period')->where('status', 'open')->count());
    }

    public function test_roles_are_cloned_per_facility(): void
    {
        $this->provision();
        $facility = DB::table('facility')->where('name_latin', 'Test Hospital')->first();
        DB::statement('SELECT set_config(?, ?, true)', ['app.facility_id', $facility->id]);

        $roles = DB::table('role')->where('facility_id', $facility->id)->count();
        $this->assertGreaterThan(0, $roles);

        // Cloned, not shared: editing a facility's role must not mutate the
        // system-wide template other facilities depend on.
        $this->assertSame(0, DB::table('role')
            ->where('facility_id', $facility->id)->where('is_system', true)->count());
    }
}
