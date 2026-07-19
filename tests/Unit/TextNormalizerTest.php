<?php

namespace Tests\Unit;

use App\Support\TextNormalizer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Verifies the PHP normalizer against the SHARED fixture that the TypeScript
 * normalizer is also tested against. Do not add cases here — add them to
 * shared/text-normalization-fixture.json so both implementations get them.
 */
class TextNormalizerTest extends TestCase
{
    private static function fixture(): array
    {
        $path = __DIR__.'/../../../shared/text-normalization-fixture.json';

        if (! is_file($path)) {
            self::fail("Shared fixture not found at {$path}. The PHP and TypeScript "
                .'normalizers must be tested against the same file.');
        }

        return json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    }

    public static function normalizeInputCases(): array
    {
        return array_map(
            fn (array $c) => [$c['name'], $c['in'], $c['out'], $c['why']],
            self::fixture()['normalizeInput']
        );
    }

    public static function searchKeyCases(): array
    {
        return array_map(
            fn (array $c) => [$c['name'], $c['in'], $c['out'], $c['why']],
            self::fixture()['searchKey']
        );
    }

    #[DataProvider('normalizeInputCases')]
    public function test_normalize_input(string $name, string $in, string $out, string $why): void
    {
        $this->assertSame($out, TextNormalizer::normalizeInput($in), "{$name}\n{$why}");
    }

    #[DataProvider('searchKeyCases')]
    public function test_search_key(string $name, string $in, string $out, string $why): void
    {
        $this->assertSame($out, TextNormalizer::searchKey($in), "{$name}\n{$why}");
    }

    public function test_null_passes_through(): void
    {
        $this->assertNull(TextNormalizer::normalizeInput(null));
        $this->assertNull(TextNormalizer::searchKey(null));
        $this->assertNull(TextNormalizer::digitsOnly(null));
    }

    /**
     * The single most important behavioural guarantee in this class: a patient
     * registered on an Afghan keyboard must be findable by a clerk on an Arabic
     * keyboard. These strings are visually identical and differ only in codepoint.
     */
    public function test_arabic_and_afghan_keyboards_produce_the_same_search_key(): void
    {
        $afghanKeyboard = 'احمد ولی';   // yeh = U+06CC
        $arabicKeyboard = 'احمد ولي';   // yeh = U+064A

        $this->assertNotSame(
            $afghanKeyboard,
            $arabicKeyboard,
            'precondition: these must be different strings, or the test proves nothing'
        );

        $this->assertSame(
            TextNormalizer::searchKey($afghanKeyboard),
            TextNormalizer::searchKey($arabicKeyboard),
            'A clerk searching on an Arabic keyboard would otherwise get zero results '
            .'and create a duplicate patient record.'
        );
    }

    /**
     * Guard against someone replacing this class with a stock Persian normalizer.
     */
    public function test_pashto_contrastive_letters_are_not_folded(): void
    {
        $this->assertStringContainsString(
            "\u{06D0}",
            TextNormalizer::searchKey('ښځې'),
            'Pashto ې (U+06D0) must not fold to ی — it is a distinct phoneme.'
        );

        $this->assertStringContainsString(
            "\u{06CD}",
            TextNormalizer::searchKey('ولسمشرۍ'),
            'Pashto ۍ (U+06CD) must not fold to ی — it is a distinct letter.'
        );
    }

    /**
     * Stored values must never be mutated by input normalization — only search
     * keys fold letters (ADR-005: name_local is authoritative and verbatim).
     */
    public function test_input_normalization_never_alters_letters(): void
    {
        foreach (['احمد ولي', 'ښاغلی', 'فاطمة', 'كابل'] as $name) {
            $this->assertSame(
                $name,
                TextNormalizer::normalizeInput($name),
                'normalizeInput must not fold letters; that is searchKey\'s job.'
            );
        }
    }

    public function test_phone_normalization(): void
    {
        $this->assertSame('+93701234567', TextNormalizer::phone('0093701234567'));
        $this->assertSame('+93701234567', TextNormalizer::phone('93701234567'));
        $this->assertSame('+93701234567', TextNormalizer::phone('0701234567'));
        // Persian digits, which is what an Afghan keyboard actually produces
        $this->assertSame('+93701234567', TextNormalizer::phone('۰۷۰۱۲۳۴۵۶۷'));
        // Unrecognised shape is returned as digits, never guessed at
        $this->assertSame('12345', TextNormalizer::phone('12345'));
    }
}
