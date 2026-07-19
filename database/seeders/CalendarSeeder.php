<?php

namespace Database\Seeders;

use App\Support\AfghanCalendar;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Seeds fiscal years and Shamsi reporting months (ADR-004).
 *
 * THE FISCAL YEAR BOUNDARY MOVED TWICE. This is why fiscal_year is a table and
 * not a formula:
 *
 *   FY1390 and earlier  1 Hamal  -> 29/30 Hoot   (≈21 Mar - 20 Mar)
 *   FY1391              1 Hamal  -> 30 Qaws      *** NINE-MONTH STUB ***
 *   FY1392 - FY1400     1 Jadi   -> 30 Qaws      (≈22 Dec - 21 Dec)
 *   FY1401 onward       1 Hamal  -> 29/30 Hoot   (reverted, Jan 2022)
 *
 * Hardcoding "the fiscal year starts on 21 March" silently misreports every
 * financial period from 1391 to 1400. The transition year 1391 was compressed
 * to nine months when the boundary moved to 1 Jadi.
 *
 * Gregorian equivalents are computed via ICU rather than hand-tabulated,
 * because Solar Hijri leap years are determined ASTRONOMICALLY (by the vernal
 * equinox), not by a closed-form rule.
 */
class CalendarSeeder extends Seeder
{
    /** Shamsi years to materialise. */
    private const FIRST_YEAR = 1380;   // ≈2001

    private const LAST_YEAR = 1420;   // ≈2041

    public function run(): void
    {
        DB::transaction(function () {
            $fiscalYearIds = $this->seedFiscalYears();
            $this->seedShamsiMonths($fiscalYearIds);
        });

        $this->command?->info(sprintf(
            'Calendar: fiscal years %d-%d and %d Shamsi months seeded.',
            self::FIRST_YEAR,
            self::LAST_YEAR,
            (self::LAST_YEAR - self::FIRST_YEAR + 1) * 12
        ));
    }

    /**
     * @return array<int, string> shamsi_year => fiscal_year uuid
     */
    private function seedFiscalYears(): array
    {
        $rows = [];
        $ids = [];

        $previousEnd = null;

        for ($year = self::FIRST_YEAR; $year <= self::LAST_YEAR; $year++) {
            [$startYear, $startMonth, $endYear, $endMonth, $isStub] = $this->boundaryFor($year);

            $start = AfghanCalendar::toGregorian($startYear, $startMonth, 1);
            // Half-open [start, nextStart): the end is the first day of the
            // following fiscal year, so adjacent years cannot overlap. The
            // EXCLUDE constraint on fiscal_year.period enforces that.
            $end = AfghanCalendar::toGregorian($endYear, $endMonth, 1);

            if ($end <= $start) {
                throw new \RuntimeException(
                    "FY{$year} ends on or before it starts: "
                    .$start->format('Y-m-d').' -> '.$end->format('Y-m-d')
                );
            }

            // A GAP is not an overlap, so the database will happily accept one.
            // Only this check catches a missing year — which is exactly the bug
            // an off-by-one in the Jadi-era boundary produces.
            if ($previousEnd !== null && $start->format('Y-m-d') !== $previousEnd) {
                throw new \RuntimeException(sprintf(
                    'Fiscal year continuity broken: FY%d starts %s but the previous '
                    .'year ended %s. Fiscal years must tile the timeline with no gaps.',
                    $year, $start->format('Y-m-d'), $previousEnd
                ));
            }
            $previousEnd = $end->format('Y-m-d');

            $months = (int) round($start->diff($end)->days / 30.44);

            $id = (string) Str::uuid();
            $ids[$year] = $id;

            $rows[] = [
                'id' => $id,
                'shamsi_year' => $year,
                'label' => 'FY'.$year
                    .($isStub ? " (transition, ~{$months} months)" : ''),
                'period' => sprintf('[%s,%s)', $start->format('Y-m-d'), $end->format('Y-m-d')),
                'is_stub' => $isStub,
            ];
        }

        DB::table('fiscal_year')->upsert(
            $rows,
            ['shamsi_year'],
            ['label', 'period', 'is_stub']
        );

        // upsert may not return ids for pre-existing rows; re-read to be safe.
        return DB::table('fiscal_year')
            ->whereBetween('shamsi_year', [self::FIRST_YEAR, self::LAST_YEAR])
            ->pluck('id', 'shamsi_year')
            ->all();
    }

    /**
     * Returns [startYear, startMonth, endYear, endMonth, isStub] for a fiscal year.
     *
     * There are TWO stub years, one at each boundary change. Missing the second
     * one produces overlapping fiscal years — which the EXCLUDE constraint on
     * fiscal_year.period rejects outright rather than letting it corrupt every
     * financial report spanning 2022.
     */
    private function boundaryFor(int $year): array
    {
        // STUB 1 — moving Hamal -> Jadi (Oct 2011).
        // FY1391 began 1 Hamal 1391 and was cut short at 1 Jadi 1391: 9 months.
        if ($year === 1391) {
            return [1391, 1, 1391, 10, true];   // 1 Hamal 1391 -> 1 Jadi 1391
        }

        // Jadi era. NOTE THE OFF-BY-ONE: fiscal year N starts on 1 Jadi of
        // Shamsi year N-1, not year N. Anchor: Afghanistan's FY1392 is
        // documented as 22 Dec 2012 - 21 Dec 2013, and 1 Jadi 1391 is Dec 2012.
        // Getting this wrong leaves a full year unaccounted for between FY1391
        // and FY1392 — which the EXCLUDE constraint does NOT catch, because a
        // gap is not an overlap. Only the contiguity assertion below catches it.
        if ($year >= 1392 && $year <= 1399) {
            return [$year - 1, 10, $year, 10, false];   // 1 Jadi (N-1) -> 1 Jadi (N)
        }

        // STUB 2 — moving Jadi -> Hamal (Jan 2022).
        // FY1400 began 1 Jadi 1399 (≈21 Dec 2020) and ran until FY1401 began at
        // Nawroz, 1 Hamal 1401 (≈21 Mar 2022) — about 15 months.
        //
        // VERIFY BEFORE FINANCIAL REPORTING RELIES ON THIS. The 9-month FY1391
        // stub and the FY1392/FY1401 boundaries are documented; how the Ministry
        // of Finance treated the Dec 2021 - Mar 2022 window is not. It may have
        // been booked as a separate short period rather than absorbed into
        // FY1400. Confirm against the MoF fiscal bulletins.
        if ($year === 1400) {
            return [1399, 10, 1401, 1, true];   // 1 Jadi 1399 -> 1 Hamal 1401
        }

        // FY1390 and earlier, and FY1401 onward: 1 Hamal to 1 Hamal.
        return [$year, 1, $year + 1, 1, false];
    }

    /**
     * @param  array<int, string>  $fiscalYearIds
     */
    private function seedShamsiMonths(array $fiscalYearIds): void
    {
        $rows = [];

        for ($year = self::FIRST_YEAR; $year <= self::LAST_YEAR; $year++) {
            $fiscalYearId = $this->fiscalYearFor($year, 1, $fiscalYearIds);

            for ($month = 1; $month <= 12; $month++) {
                $start = AfghanCalendar::toGregorian($year, $month, 1);
                $end = $month === 12
                    ? AfghanCalendar::toGregorian($year + 1, 1, 1)
                    : AfghanCalendar::toGregorian($year, $month + 1, 1);

                // MoPH routine-reporting mapping, NOT the exact span: "each
                // Shamsi month is matched with the Gregorian month of which it
                // contains 20 days". Hamal->April ... Hoot->March.
                $reporting = AfghanCalendar::reportingMonthFor($year, $month);

                $rows[] = [
                    'id' => (string) Str::uuid(),
                    'shamsi_year' => $year,
                    'month_no' => $month,
                    'name_dari' => AfghanCalendar::MONTHS[$month][0],
                    'name_latin' => AfghanCalendar::MONTHS[$month][1],
                    'name_pashto' => AfghanCalendar::MONTHS[$month][2],
                    'gregorian_period' => sprintf(
                        '[%s,%s)', $start->format('Y-m-d'), $end->format('Y-m-d')
                    ),
                    'reporting_gregorian_month' => $reporting->format('Y-m-d'),
                    'fiscal_year_id' => $this->fiscalYearFor($year, $month, $fiscalYearIds)
                        ?? $fiscalYearId,
                ];
            }
        }

        foreach (array_chunk($rows, 200) as $chunk) {
            DB::table('shamsi_month')->upsert(
                $chunk,
                ['shamsi_year', 'month_no'],
                ['name_dari', 'name_latin', 'name_pashto', 'gregorian_period',
                    'reporting_gregorian_month', 'fiscal_year_id']
            );
        }
    }

    /**
     * Which fiscal year does a given Shamsi month fall into?
     *
     * During the 1 Jadi era (FY1392-FY1400) months 1-9 of Shamsi year N belong
     * to fiscal year N-1, because that fiscal year began in Jadi of N-1.
     *
     * @param  array<int, string>  $ids
     */
    private function fiscalYearFor(int $shamsiYear, int $month, array $ids): ?string
    {
        // The Jadi era ends with FY1400, which was truncated at 1 Hamal 1401.
        // So every month of Shamsi 1401 already belongs to FY1401 — including
        // months 1-9, which in the Jadi era would have belonged to the prior
        // fiscal year.
        $jadiEra = $shamsiYear >= 1392 && $shamsiYear <= 1400;

        $fyYear = ($jadiEra && $month < 10) ? $shamsiYear - 1 : $shamsiYear;

        return $ids[$fyYear] ?? $ids[$shamsiYear] ?? null;
    }
}
