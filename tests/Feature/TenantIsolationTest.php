<?php

namespace Tests\Feature;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Proves Row-Level Security is actually engaged on the connection Laravel uses.
 *
 * This is the test that is easy to skip and expensive to skip. RLS fails
 * SILENTLY in two ways:
 *
 *   1. A superuser bypasses RLS unconditionally. If the app connects as
 *      `postgres`, every policy in the schema is inert and every facility can
 *      read every other facility's patients. No error is raised.
 *   2. `ENABLE ROW LEVEL SECURITY` does not apply to the table OWNER unless
 *      `FORCE` is also set. Since the migration user usually owns the tables,
 *      and the app often connects as that same user, RLS commonly does nothing
 *      in exactly the setup people ship.
 *
 * So the first two tests here assert the PREMISE — that the connection is not
 * privileged — before any isolation claim is made.
 */
class TenantIsolationTest extends TestCase
{
    private const FACILITY_A = '11111111-1111-1111-1111-111111111111';

    private const FACILITY_B = '22222222-2222-2222-2222-222222222222';

    protected function setUp(): void
    {
        parent::setUp();

        if (DB::connection()->getDriverName() !== 'pgsql') {
            $this->markTestSkipped('Tenant isolation is enforced by PostgreSQL RLS.');
        }

        DB::beginTransaction();

        DB::table('facility')->insert([
            ['id' => self::FACILITY_A, 'name_local' => 'شفاخانه الف',
                'name_latin' => 'Hospital A', 'facility_type' => 'provincial_hospital'],
            ['id' => self::FACILITY_B, 'name_local' => 'شفاخانه ب',
                'name_latin' => 'Hospital B', 'facility_type' => 'district_hospital'],
        ]);
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }

    private function asFacility(string $facilityId): void
    {
        // Transaction-local, exactly as SetFacilityContext middleware does it.
        DB::statement('SELECT set_config(?, ?, true)', ['app.facility_id', $facilityId]);
    }

    private function createPerson(string $facilityId, string $name): void
    {
        DB::table('person')->insert([
            'facility_id' => $facilityId,
            'name_local' => $name,
            'given_name' => $name,
            'gender' => 'M',
            'birth_date' => '1990-01-01',
        ]);
    }

    public function test_application_connection_is_not_a_superuser(): void
    {
        $role = DB::selectOne(
            'SELECT rolsuper, rolbypassrls FROM pg_roles WHERE rolname = current_user'
        );

        $this->assertFalse((bool) $role->rolsuper,
            'The application is connected as a SUPERUSER. Every RLS policy in the '
            .'schema is silently inert and all tenant isolation is fake.');

        $this->assertFalse((bool) $role->rolbypassrls,
            'The application role has BYPASSRLS. Tenant isolation is not enforced.');
    }

    public function test_rls_is_enabled_and_forced_on_patient_tables(): void
    {
        $tables = ['person', 'patient', 'encounter', 'charge_item', 'invoice'];

        foreach ($tables as $table) {
            $row = DB::selectOne(
                'SELECT relrowsecurity, relforcerowsecurity
                   FROM pg_class c JOIN pg_namespace n ON n.oid = c.relnamespace
                  WHERE n.nspname = ? AND c.relname = ?',
                ['public', $table]
            );

            $this->assertTrue((bool) $row->relrowsecurity, "RLS is not enabled on {$table}");
            $this->assertTrue((bool) $row->relforcerowsecurity,
                "RLS is not FORCED on {$table} — the table owner would bypass it");
        }
    }

    public function test_a_facility_sees_only_its_own_patients(): void
    {
        $this->asFacility(self::FACILITY_A);
        $this->createPerson(self::FACILITY_A, 'احمد');
        $this->createPerson(self::FACILITY_A, 'محمود');

        $this->assertSame(2, DB::table('person')->count());

        $this->asFacility(self::FACILITY_B);

        $this->assertSame(0, DB::table('person')->count(),
            'DATA LEAK: facility B can read facility A patient records.');
    }

    public function test_unset_facility_context_fails_closed(): void
    {
        $this->asFacility(self::FACILITY_A);
        $this->createPerson(self::FACILITY_A, 'احمد');

        // Simulates an unauthenticated request, or middleware that did not run.
        DB::statement("SELECT set_config('app.facility_id', '', true)");

        $this->assertSame(0, DB::table('person')->count(),
            'With no facility context the query must return nothing. Returning '
            .'rows means an unauthenticated path can read patient data.');
    }

    public function test_cannot_write_a_row_belonging_to_another_facility(): void
    {
        $this->asFacility(self::FACILITY_A);

        $this->expectException(QueryException::class);

        // A WITH CHECK violation. Without it a tenant could insert rows it
        // cannot itself read — invisible cross-tenant writes.
        $this->createPerson(self::FACILITY_B, 'نفوذی');
    }

    public function test_ledger_tables_reject_updates_through_the_app_connection(): void
    {
        $this->asFacility(self::FACILITY_A);

        DB::table('audit_log')->insert([
            'facility_id' => self::FACILITY_A,
            'action' => 'read',
            'table_name' => 'patient',
            'username' => 'tester',
        ]);

        $this->expectException(QueryException::class);
        DB::table('audit_log')->update(['username' => 'tampered']);
    }
}
