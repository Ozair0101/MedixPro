<?php

namespace App\Support;

use DateTimeImmutable;
use DateTimeZone;
use IntlCalendar;
use IntlDateFormatter;
use InvalidArgumentException;

/**
 * Solar Hijri (Shamsi) calendar for Afghanistan (ADR-004).
 *
 * Storage is always Gregorian/UTC; this class is the presentation and
 * reporting layer. Jalali-as-text would break indexing, range scans and
 * interval arithmetic.
 *
 * AFGHAN MONTH NAMES, NOT IRANIAN. Afghanistan uses the zodiac names — Hamal,
 * Sawr, Jawza — where Iran uses Farvardin, Ordibehesht, Khordad. This trips up
 * every off-the-shelf library: `morilog/jalali` and `hekmatinasser/verta` are
 * Iranian-names-only, and ICU leaks Iranian names into an English locale
 * (`en-US-u-ca-persian` renders "Tir 27, 1405"). Hence the explicit table below.
 *
 * TWO CONVERSION RULES, deliberately not unified:
 *
 *   toShamsi()             Exact date conversion. Use for births, admissions,
 *                          notifiable-disease alerts — anything punctual.
 *
 *   reportingMonthFor()    MoPH's fixed month mapping for ROUTINE AGGREGATES:
 *                          "each Shamsi month is matched with the Gregorian
 *                          month of which it contains 20 days." Hamal↔April,
 *                          … Hoot↔March. Use for MIAR/HMIR only.
 *
 * Using the wrong one silently misreports. See docs/00-architecture-decisions.md.
 */
final class AfghanCalendar
{
    public const TIMEZONE = 'Asia/Kabul';   // UTC+04:30, no DST, ever

    /**
     * month number => [dari, latin, pashto, typical gregorian month]
     *
     * The Gregorian column is the MoPH reporting mapping, not the actual span.
     */
    public const MONTHS = [
        1 => ['حمل',   'Hamal',   'وری',      4],
        2 => ['ثور',   'Sawr',    'غویی',     5],
        3 => ['جوزا',  'Jawza',   'غبرګولی',  6],
        4 => ['سرطان', 'Saratan', 'چنګاښ',    7],
        5 => ['اسد',   'Asad',    'زمری',     8],
        6 => ['سنبله', 'Sunbula', 'وږی',      9],
        7 => ['میزان', 'Mizan',   'تله',      10],
        8 => ['عقرب',  'Aqrab',   'لړم',      11],
        9 => ['قوس',   'Qaws',    'لیندۍ',    12],
        10 => ['جدی',   'Jadi',    'مرغومی',   1],
        11 => ['دلو',   'Dalwa',   'سلواغه',   2],
        12 => ['حوت',   'Hoot',    'کب',       3],
    ];

    /**
     * Exact Gregorian -> Shamsi conversion.
     *
     * @return array{year:int, month:int, day:int}
     */
    public static function toShamsi(DateTimeImmutable $date): array
    {
        $cal = self::calendar();
        $cal->setTime($date->getTimestamp() * 1000);

        return [
            'year' => $cal->get(IntlCalendar::FIELD_YEAR),
            // ICU months are 0-based.
            'month' => $cal->get(IntlCalendar::FIELD_MONTH) + 1,
            'day' => $cal->get(IntlCalendar::FIELD_DAY_OF_MONTH),
        ];
    }

    /**
     * Exact Shamsi -> Gregorian conversion.
     */
    public static function toGregorian(int $year, int $month, int $day): DateTimeImmutable
    {
        self::assertValidMonth($month);

        $cal = self::calendar();
        $cal->clear();
        // setDate(), not set(): passing more than 2 arguments to
        // IntlCalendar::set() is deprecated as of PHP 8.4.
        $cal->setDate($year, $month - 1, $day);

        return (new DateTimeImmutable('@'.intdiv((int) $cal->getTime(), 1000)))
            ->setTimezone(new DateTimeZone(self::TIMEZONE));
    }

    /**
     * Format a date for display, e.g. "۲۷ سرطان ۱۴۰۵".
     *
     * @param  string  $locale  fa-AF (Dari), ps-AF (Pashto) or en
     */
    public static function format(
        DateTimeImmutable $date,
        string $locale = 'fa-AF',
        bool $withDigitsLocalized = true
    ): string {
        $s = self::toShamsi($date);
        $name = self::monthName($s['month'], $locale);

        $day = (string) $s['day'];
        $year = (string) $s['year'];

        if ($withDigitsLocalized && $locale !== 'en') {
            $day = self::toLocalizedDigits($day);
            $year = self::toLocalizedDigits($year);
        }

        return "{$day} {$name} {$year}";
    }

    public static function monthName(int $month, string $locale = 'fa-AF'): string
    {
        self::assertValidMonth($month);

        return match ($locale) {
            'ps-AF' => self::MONTHS[$month][2] ?: self::MONTHS[$month][0],
            'en' => self::MONTHS[$month][1],
            default => self::MONTHS[$month][0],
        };
    }

    /**
     * MoPH ROUTINE-REPORTING mapping only.
     *
     * Returns the first day of the Gregorian month that a given Shamsi month
     * maps to for MIAR/HMIR aggregation. Hamal->April, Hoot->March (of the
     * following Gregorian year, since Hoot straddles the boundary).
     *
     * Do NOT use this for punctual events — use toGregorian().
     */
    public static function reportingMonthFor(int $shamsiYear, int $shamsiMonth): DateTimeImmutable
    {
        self::assertValidMonth($shamsiMonth);

        $gregorianMonth = self::MONTHS[$shamsiMonth][3];

        // A Shamsi year starting at Nawroz (late March) spans two Gregorian
        // years. Months 1-9 (Hamal..Qaws) fall in the first; months 10-12
        // (Jadi, Dalwa, Hoot) fall in the next.
        $gregorianYear = $shamsiYear + 621 + ($shamsiMonth >= 10 ? 1 : 0);

        return new DateTimeImmutable(
            sprintf('%04d-%02d-01 00:00:00', $gregorianYear, $gregorianMonth),
            new DateTimeZone(self::TIMEZONE)
        );
    }

    /**
     * Render ASCII digits as Extended Arabic-Indic for display.
     *
     * Display only — never persist the result. TextNormalizer folds these back
     * on the way in precisely because they break ORDER BY and index lookups.
     */
    public static function toLocalizedDigits(string $value): string
    {
        return strtr($value, [
            '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴',
            '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹',
        ]);
    }

    private static function assertValidMonth(int $month): void
    {
        if ($month < 1 || $month > 12) {
            throw new InvalidArgumentException("Shamsi month must be 1-12, got {$month}");
        }
    }

    /**
     * VERIFIED GOTCHA (PHP 8.4 / ICU 77): IntlDateFormatter SILENTLY IGNORES
     * the calendar keyword in a locale string and returns Gregorian output in
     * Persian script. The calendar must be passed as an IntlCalendar instance.
     * That is why this helper exists rather than a one-line formatter call.
     */
    private static function calendar(): IntlCalendar
    {
        $cal = IntlCalendar::createInstance(
            new DateTimeZone(self::TIMEZONE),
            'fa_AF@calendar=persian'
        );

        if ($cal === null) {
            throw new \RuntimeException(
                'Failed to create Persian IntlCalendar. Is ext-intl installed?'
            );
        }

        return $cal;
    }

    /**
     * Exposed so tests can assert the ICU gotcha stays fixed.
     */
    public static function formatterSanityCheck(): string
    {
        $fmt = new IntlDateFormatter(
            'fa-AF',
            IntlDateFormatter::LONG,
            IntlDateFormatter::NONE,
            self::TIMEZONE,
            self::calendar()          // <-- the 6th argument that actually matters
        );

        return $fmt->format(new DateTimeImmutable('2026-07-18', new DateTimeZone('UTC')));
    }
}
