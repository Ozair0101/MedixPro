<?php

namespace Tests\Feature;

use App\Support\AuditLogger;
use Database\Seeders\ReferenceDataSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuditTrailTest extends TestCase
{
    private const FACILITY = '33333333-3333-3333-3333-333333333333';

    protected function setUp(): void
    {
        parent::setUp();

        if (DB::connection()->getDriverName() !== 'pgsql') {
            $this->markTestSkipped('The audit trail targets the PostgreSQL schema.');
        }

        DB::beginTransaction();
        AuditLogger::flushCache();

        DB::table('facility')->insert([
            'id' => self::FACILITY, 'name_local' => 'شفاخانه تست',
            'name_latin' => 'Test Hospital', 'facility_type' => 'district_hospital',
        ]);

        DB::statement('SELECT set_config(?, ?, true)', ['app.facility_id', self::FACILITY]);
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }

    public function test_a_read_is_recorded(): void
    {
        $patientId = (string) Str::uuid();

        AuditLogger::recordRead('patient', $patientId, $patientId);

        $row = DB::table('audit_log')->where('record_id', $patientId)->first();

        $this->assertNotNull($row, 'Reads must be logged: a SELECT fires no database '
            .'trigger, so nothing else can capture them.');
        $this->assertSame('read', $row->action);
        $this->assertSame($patientId, $row->patient_id);
    }

    public function test_an_override_requires_and_records_a_reason(): void
    {
        $encounterId = (string) Str::uuid();

        AuditLogger::recordOverride(
            'encounter', $encounterId,
            'No female physician on duty; life-threatening presentation.'
        );

        $row = DB::table('audit_log')->where('record_id', $encounterId)->first();

        $this->assertSame('override', $row->action);
        $this->assertStringContainsString('No female physician', $row->override_reason,
            'An override without a recorded justification is indistinguishable '
            .'from a mistake.');
    }

    public function test_the_trail_cannot_be_rewritten(): void
    {
        AuditLogger::record('read', 'patient', (string) Str::uuid());

        $this->expectException(QueryException::class);
        DB::table('audit_log')->update(['username' => 'someone_else']);
    }

    public function test_the_trail_cannot_be_erased(): void
    {
        AuditLogger::record('read', 'patient', (string) Str::uuid());

        $this->expectException(QueryException::class);
        DB::table('audit_log')->delete();
    }

    public function test_audit_failure_never_blocks_clinical_work(): void
    {
        // No facility context: the write cannot be attributed, so it is dropped.
        DB::statement("SELECT set_config('app.facility_id', '', true)");

        // The point is that this does NOT throw. A hospital that cannot register
        // a patient because auditing failed is worse than an incomplete trail.
        AuditLogger::record('read', 'patient', (string) Str::uuid());

        $this->assertTrue(true, 'Audit failures must be swallowed, not propagated.');
    }

    public function test_sensitivity_lookup_fails_safe(): void
    {
        $this->seed(ReferenceDataSeeder::class);
        AuditLogger::flushCache();

        $this->assertTrue(AuditLogger::isSensitive('patient.read'));
        $this->assertTrue(AuditLogger::isSensitive('clinical.reproductive.read'));
        $this->assertFalse(AuditLogger::isSensitive('patient.create'));

        // An unknown permission is treated as sensitive: better to over-log than
        // to silently omit an access that mattered.
        $this->assertTrue(AuditLogger::isSensitive('some.permission.that.does.not.exist'));
    }

    public function test_audit_rows_are_tenant_scoped(): void
    {
        AuditLogger::record('read', 'patient', (string) Str::uuid());
        $this->assertSame(1, DB::table('audit_log')->count());

        $other = (string) Str::uuid();
        DB::table('facility')->insert([
            'id' => $other, 'name_local' => 'دیگر', 'name_latin' => 'Other',
            'facility_type' => 'clinic',
        ]);
        DB::statement('SELECT set_config(?, ?, true)', ['app.facility_id', $other]);

        $this->assertSame(0, DB::table('audit_log')->count(),
            'Another facility must not be able to read our audit trail.');
    }
}
