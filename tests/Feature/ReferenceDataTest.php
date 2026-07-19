<?php

namespace Tests\Feature;

use Database\Seeders\CalendarSeeder;
use Database\Seeders\GeographySeeder;
use Database\Seeders\ReferenceDataSeeder;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Locks in the reference data that everything else depends on.
 *
 * The fiscal-year assertions exist because getting them wrong is silent: an
 * off-by-one in the Jadi-era boundary leaves a whole year unaccounted for, and
 * a gap is not an overlap, so no database constraint catches it. Every
 * financial report spanning that period would simply be wrong.
 */
class ReferenceDataTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (DB::connection()->getDriverName() !== 'pgsql') {
            $this->markTestSkipped('Reference data targets the PostgreSQL schema.');
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

    public function test_geography_matches_the_ocha_gazetteer(): void
    {
        // 34 provinces and 401 ADM2 units (367 districts + 33 provincial
        // centres + 1 capital). This 401 reconciles the conflicting public
        // figures — 325 / 398 / 412 / 421 — that circulate online.
        $this->assertSame(34, DB::table('geo_province')->count());
        $this->assertSame(401, DB::table('geo_district')->count());
    }

    public function test_district_pcodes_nest_under_their_province(): void
    {
        $orphans = DB::table('geo_district')
            ->whereRaw('left(pcode, 4) <> province_pcode')
            ->count();

        $this->assertSame(0, $orphans,
            'P-codes are strictly hierarchical: AF0501 must belong to AF05.');
    }

    public function test_transliteration_aliases_resolve(): void
    {
        // The COD spells these Hirat / Hilmand / Panjsher / Maidan Wardak.
        // Users type Herat / Helmand / Panjshir / Wardak. Without aliases,
        // registration search silently returns nothing.
        foreach (['Herat' => 'Hirat', 'Helmand' => 'Hilmand',
            'Panjshir' => 'Panjsher', 'Wardak' => 'Maidan Wardak'] as $typed => $cod) {
            $pcode = DB::table('geo_alias')->where('alias', $typed)->value('pcode');

            $this->assertNotNull($pcode, "No alias registered for '{$typed}'");

            $this->assertSame($cod,
                DB::table('geo_province')->where('pcode', $pcode)->value('name_latin'),
                "Alias '{$typed}' should resolve to '{$cod}'");
        }
    }

    public function test_fiscal_years_are_gapless(): void
    {
        $years = DB::table('fiscal_year')->orderBy('shamsi_year')
            ->get(['shamsi_year', 'period']);

        $previousEnd = null;

        foreach ($years as $fy) {
            // Postgres renders a daterange as [start,end)
            [$start, $end] = $this->parseRange($fy->period);

            if ($previousEnd !== null) {
                $this->assertSame($previousEnd, $start,
                    "FY{$fy->shamsi_year} starts {$start} but the previous fiscal year "
                    ."ended {$previousEnd}. Fiscal years must tile the timeline: a gap "
                    .'means transactions in between belong to no fiscal year at all.');
            }

            $previousEnd = $end;
        }
    }

    public function test_both_fiscal_year_transitions_are_recorded(): void
    {
        // The boundary moved twice: Hamal -> Jadi (Oct 2011), then back to
        // Hamal (Jan 2022). Each move produced a short/long transition year.
        $stubs = DB::table('fiscal_year')->where('is_stub', true)
            ->orderBy('shamsi_year')->pluck('shamsi_year')->all();

        $this->assertSame([1391, 1400], $stubs,
            'Exactly two transition years are expected: FY1391 and FY1400.');
    }

    public function test_known_fiscal_year_anchors(): void
    {
        // FY1391 began at Nawroz 1391 and was cut short at 1 Jadi: nine months.
        [$start, $end] = $this->parseRange(
            DB::table('fiscal_year')->where('shamsi_year', 1391)->value('period')
        );
        $this->assertSame('2012-03-20', $start);
        $this->assertSame('2012-12-21', $end);

        // Afghanistan's FY1392 is documented as running 22 Dec 2012 to
        // 21 Dec 2013. Half-open [2012-12-21, 2013-12-22) covers exactly that.
        [$start, $end] = $this->parseRange(
            DB::table('fiscal_year')->where('shamsi_year', 1392)->value('period')
        );
        $this->assertSame('2012-12-21', $start);

        // FY1401 reverted to Nawroz: 21 March 2022.
        [$start] = $this->parseRange(
            DB::table('fiscal_year')->where('shamsi_year', 1401)->value('period')
        );
        $this->assertSame('2022-03-21', $start);
    }

    public function test_shamsi_months_use_afghan_names(): void
    {
        $names = DB::table('shamsi_month')->where('shamsi_year', 1405)
            ->orderBy('month_no')->pluck('name_latin')->all();

        $this->assertSame([
            'Hamal', 'Sawr', 'Jawza', 'Saratan', 'Asad', 'Sunbula',
            'Mizan', 'Aqrab', 'Qaws', 'Jadi', 'Dalwa', 'Hoot',
        ], $names, 'Afghanistan uses the zodiac month names, not the Iranian ones.');
    }

    public function test_moph_reporting_months_cover_the_gregorian_year(): void
    {
        // MoPH maps each Shamsi month to one Gregorian month for routine
        // aggregates. Across a year those must be twelve distinct months.
        $months = DB::table('shamsi_month')->where('shamsi_year', 1405)
            ->pluck('reporting_gregorian_month')
            ->map(fn ($d) => (int) substr((string) $d, 5, 2))
            ->sort()->values()->all();

        $this->assertSame(range(1, 12), $months);
    }

    public function test_sensitive_permissions_are_flagged_for_read_auditing(): void
    {
        // A SELECT fires no database trigger, so read auditing has to happen in
        // the application — driven by this flag (ADR-010).
        foreach (['patient.read', 'observation.read',
            'clinical.reproductive.read', 'audit.read'] as $code) {
            $this->assertTrue(
                (bool) DB::table('permission')->where('code', $code)->value('is_sensitive'),
                "Permission '{$code}' must be marked sensitive so reads are audited."
            );
        }
    }

    public function test_every_role_permission_refers_to_a_real_permission(): void
    {
        $dangling = DB::table('role_permission as rp')
            ->leftJoin('permission as p', 'p.code', '=', 'rp.permission_code')
            ->whereNull('p.code')
            ->count();

        $this->assertSame(0, $dangling);
    }

    public function test_national_id_validation_is_format_only(): void
    {
        // There is no public check-digit specification for the e-Tazkira, so a
        // guessed checksum would reject valid patients (ADR-006).
        $regex = DB::table('identifier_type')->where('code', 'e_tazkira')
            ->value('validation_regex');

        $this->assertSame('^[0-9]{13}$', $regex);

        // Paper tazkira has no authoritative public format at all.
        $this->assertNull(
            DB::table('identifier_type')->where('code', 'paper_tazkira')
                ->value('validation_regex')
        );
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function parseRange(string $range): array
    {
        preg_match('/^\[([^,]+),([^)]+)\)$/', $range, $m);

        return [trim($m[1], '"'), trim($m[2], '"')];
    }
}
