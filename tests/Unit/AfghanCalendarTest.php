<?php

namespace Tests\Unit;

use App\Support\AfghanCalendar;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\TestCase;

class AfghanCalendarTest extends TestCase
{
    public function test_nawroz_is_the_first_of_hamal(): void
    {
        // 21 March 2026 is Nawroz — 1 Hamal 1405.
        $s = AfghanCalendar::toShamsi(
            new DateTimeImmutable('2026-03-21 12:00:00', new DateTimeZone('Asia/Kabul'))
        );

        $this->assertSame(1405, $s['year']);
        $this->assertSame(1, $s['month'], 'Nawroz must fall in Hamal');
        $this->assertSame(1, $s['day']);
    }

    public function test_round_trip_conversion(): void
    {
        $original = new DateTimeImmutable('2026-07-18 12:00:00', new DateTimeZone('Asia/Kabul'));

        $s = AfghanCalendar::toShamsi($original);
        $back = AfghanCalendar::toGregorian($s['year'], $s['month'], $s['day']);

        $this->assertSame(
            $original->format('Y-m-d'),
            $back->format('Y-m-d'),
            'Gregorian -> Shamsi -> Gregorian must be lossless'
        );
    }

    /**
     * The single most important assertion in this file. Afghanistan uses the
     * zodiac month names; Iran does not. Every off-the-shelf Jalali library
     * ships Iranian names, and ICU leaks them into an English locale.
     */
    public function test_month_names_are_afghan_not_iranian(): void
    {
        $this->assertSame('Hamal', AfghanCalendar::monthName(1, 'en'));
        $this->assertSame('Sawr', AfghanCalendar::monthName(2, 'en'));
        $this->assertSame('Jawza', AfghanCalendar::monthName(3, 'en'));
        $this->assertSame('Saratan', AfghanCalendar::monthName(4, 'en'));

        $iranian = ['Farvardin', 'Ordibehesht', 'Khordad', 'Tir', 'Mordad',
            'Shahrivar', 'Mehr', 'Aban', 'Azar', 'Dey', 'Bahman', 'Esfand'];

        for ($m = 1; $m <= 12; $m++) {
            $this->assertNotContains(
                AfghanCalendar::monthName($m, 'en'),
                $iranian,
                "Month {$m} returned an Iranian name. Afghanistan uses zodiac names."
            );
        }
    }

    public function test_dari_and_pashto_month_names(): void
    {
        $this->assertSame('حمل', AfghanCalendar::monthName(1, 'fa-AF'));
        $this->assertSame('وری', AfghanCalendar::monthName(1, 'ps-AF'));
        $this->assertSame('حوت', AfghanCalendar::monthName(12, 'fa-AF'));
        $this->assertSame('کب', AfghanCalendar::monthName(12, 'ps-AF'));
    }

    /**
     * MoPH §1.9: routine monthly aggregates use a FIXED month mapping, not
     * exact date conversion. Hamal maps to April, Hoot to March.
     */
    public function test_moph_reporting_month_mapping(): void
    {
        // Hamal 1405 -> April 2026
        $hamal = AfghanCalendar::reportingMonthFor(1405, 1);
        $this->assertSame('2026-04', $hamal->format('Y-m'));

        // Qaws (month 9) -> December, same Gregorian year
        $qaws = AfghanCalendar::reportingMonthFor(1405, 9);
        $this->assertSame('2026-12', $qaws->format('Y-m'));

        // Jadi (month 10) -> January of the FOLLOWING Gregorian year
        $jadi = AfghanCalendar::reportingMonthFor(1405, 10);
        $this->assertSame('2027-01', $jadi->format('Y-m'));

        // Hoot (month 12) -> March of the following Gregorian year
        $hoot = AfghanCalendar::reportingMonthFor(1405, 12);
        $this->assertSame('2027-03', $hoot->format('Y-m'));
    }

    public function test_reporting_mapping_covers_all_twelve_gregorian_months(): void
    {
        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[] = (int) AfghanCalendar::reportingMonthFor(1405, $m)->format('n');
        }

        sort($months);
        $this->assertSame(range(1, 12), $months,
            'The twelve Shamsi months must map onto twelve distinct Gregorian months');
    }

    public function test_localized_digits_for_display(): void
    {
        $this->assertSame('۱۴۰۵', AfghanCalendar::toLocalizedDigits('1405'));
    }

    public function test_formatted_output(): void
    {
        $formatted = AfghanCalendar::format(
            new DateTimeImmutable('2026-07-18 12:00:00', new DateTimeZone('Asia/Kabul')),
            'fa-AF'
        );

        // 18 July 2026 falls in Saratan 1405.
        $this->assertStringContainsString('سرطان', $formatted);
        $this->assertStringContainsString('۱۴۰۵', $formatted);
    }

    public function test_invalid_month_rejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        AfghanCalendar::monthName(13);
    }

    /**
     * Kabul is UTC+04:30 with no DST, ever. The half-hour offset breaks
     * integer-hour assumptions elsewhere, so pin it here.
     */
    public function test_kabul_offset_is_four_thirty_year_round(): void
    {
        $tz = new DateTimeZone(AfghanCalendar::TIMEZONE);

        foreach (['2026-01-15', '2026-07-15'] as $date) {
            $offset = $tz->getOffset(new DateTimeImmutable($date));
            $this->assertSame(16200, $offset, "Kabul must be UTC+04:30 on {$date}");
        }
    }
}
