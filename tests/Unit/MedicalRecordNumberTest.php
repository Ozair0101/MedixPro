<?php

namespace Tests\Unit;

use App\Support\MedicalRecordNumber as MRN;
use PHPUnit\Framework\TestCase;

class MedicalRecordNumberTest extends TestCase
{
    public function test_a_generated_mrn_validates(): void
    {
        $this->assertTrue(MRN::isValid(MRN::withCheckDigit('MRN-000123')));
        $this->assertTrue(MRN::isValid(MRN::withCheckDigit('MRN-000001')));
        $this->assertTrue(MRN::isValid(MRN::withCheckDigit('MRN-999999')));
    }

    /**
     * The whole point of the check digit: a clerk who mistypes one character
     * gets told, instead of silently opening someone else's chart.
     */
    public function test_every_single_digit_error_is_caught(): void
    {
        $valid = MRN::withCheckDigit('MRN-004821');
        $digits = preg_replace('/\D+/', '', $valid);

        for ($position = 0; $position < strlen($digits); $position++) {
            for ($replacement = 0; $replacement <= 9; $replacement++) {
                if ((int) $digits[$position] === $replacement) {
                    continue;
                }

                $corrupted = $digits;
                $corrupted[$position] = (string) $replacement;

                $this->assertFalse(
                    MRN::checkDigit($corrupted) === 0,
                    "A single-digit error at position {$position} went undetected: "
                    ."{$digits} -> {$corrupted}"
                );
            }
        }
    }

    /**
     * Damm is used instead of Luhn precisely for this: Luhn fails to detect the
     * transposition 09 <-> 90, which is a common hurried-typing error.
     */
    public function test_every_adjacent_transposition_is_caught(): void
    {
        $digits = preg_replace('/\D+/', '', MRN::withCheckDigit('MRN-010923'));

        $undetected = [];

        for ($i = 0; $i < strlen($digits) - 1; $i++) {
            if ($digits[$i] === $digits[$i + 1]) {
                continue;   // swapping identical digits is a no-op
            }

            $swapped = $digits;
            [$swapped[$i], $swapped[$i + 1]] = [$swapped[$i + 1], $swapped[$i]];

            if (MRN::checkDigit($swapped) === 0) {
                $undetected[] = "{$digits} -> {$swapped}";
            }
        }

        $this->assertSame([], $undetected,
            'Adjacent transpositions must all be detected: '.implode(', ', $undetected));
    }

    public function test_the_specific_transposition_luhn_misses(): void
    {
        // 09 <-> 90 is the documented Luhn blind spot.
        $a = MRN::checkDigit('09');
        $b = MRN::checkDigit('90');

        $this->assertNotSame($a, $b,
            'Damm must distinguish 09 from 90; Luhn does not.');
    }

    public function test_persian_digits_are_accepted(): void
    {
        $valid = MRN::withCheckDigit('MRN-000123');

        // What an Afghan keyboard actually produces.
        $persian = strtr($valid, [
            '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴',
            '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹',
        ]);

        $this->assertNotSame($valid, $persian, 'precondition: the strings differ');
        $this->assertTrue(MRN::isValid($persian),
            'A patient card read on an Afghan keyboard must validate.');
    }

    public function test_normalization_collapses_the_ways_people_type_it(): void
    {
        $canonical = MRN::normalize('MRN-000123-4');

        foreach ([
            'MRN-000123-4',
            'mrn 000123 4',
            '0001234',
            'MRN0001234',
            '۰۰۰۱۲۳۴',
        ] as $typed) {
            $this->assertSame($canonical, MRN::normalize($typed),
                "'{$typed}' must resolve to the same lookup key.");
        }
    }

    public function test_rubbish_is_rejected(): void
    {
        $this->assertFalse(MRN::isValid(null));
        $this->assertFalse(MRN::isValid(''));
        $this->assertFalse(MRN::isValid('MRN-'));
        $this->assertFalse(MRN::isValid('not-a-number'));
        $this->assertFalse(MRN::isValid('MRN-000123-9'));   // wrong check digit
    }

    public function test_normalize_returns_null_when_there_is_nothing_to_normalize(): void
    {
        $this->assertNull(MRN::normalize(null));
        $this->assertNull(MRN::normalize('no digits here'));
    }
}
