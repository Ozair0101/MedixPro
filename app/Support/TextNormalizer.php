<?php

namespace App\Support;

/**
 * Text normalization for Dari, Pashto and Latin input (ADR-007).
 *
 * Two deliberately separate operations:
 *
 *   normalizeInput()  Applied to ALL inbound text before it reaches a model.
 *                     Folds digits and strips invisible control characters.
 *                     NEVER alters letters, so a stored name stays verbatim.
 *
 *   searchKey()       Applied ONLY when writing a *_search column. Additionally
 *                     folds visually-identical letters so that a name typed on
 *                     an Arabic keyboard matches one typed on an Afghan keyboard.
 *
 * The TypeScript mirror lives at Hospital-MIS/src/lib/textNormalizer.ts.
 * Both are verified against shared/text-normalization-fixture.json. If they
 * diverge, a patient found offline is not found online.
 *
 * PASHTO WARNING: do not "simplify" this by dropping in a stock Persian
 * normalizer. Persian normalizers fold ې (U+06D0) and ۍ (U+06CD) to ی. In
 * Pashto those are distinct, phonemically contrastive letters, and folding
 * them corrupts the text. The fixture has guard cases that will fail loudly.
 */
final class TextNormalizer
{
    /** Extended Arabic-Indic (Dari/Pashto keyboards) → ASCII. */
    private const EXTENDED_ARABIC_INDIC = [
        '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
        '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
    ];

    /** Arabic-Indic → ASCII. Different codepoints; both occur in practice. */
    private const ARABIC_INDIC = [
        '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
        '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
    ];

    /** Numeric punctuation. */
    private const NUMERIC_PUNCTUATION = [
        '٫' => '.',   // U+066B arabic decimal separator
        '٬' => '',    // U+066C arabic thousands separator
    ];

    /**
     * Invisible characters stripped from ALL input.
     *
     * U+202A–U+202E are deprecated bidi embedding/override characters and are a
     * Trojan-Source-class spoofing vector — text can be made to render in an
     * order different from how it is stored. They are stripped, never preserved.
     */
    private const INVISIBLE = [
        "\u{200E}", // LRM
        "\u{200F}", // RLM
        "\u{202A}", "\u{202B}", "\u{202C}", "\u{202D}", "\u{202E}",
        "\u{2066}", "\u{2067}", "\u{2068}", "\u{2069}", // isolates
        "\u{FEFF}", // BOM / zero-width no-break space
    ];

    /**
     * Letter folding for SEARCH KEYS ONLY.
     *
     * Every entry here exists because two visually identical glyphs have
     * different codepoints, and a clerk cannot tell which one they typed.
     */
    private const SEARCH_LETTER_FOLD = [
        // Yeh family → Farsi yeh (U+06CC). THE critical case.
        'ي' => 'ی',   // U+064A arabic yeh
        'ى' => 'ی',   // U+0649 alef maksura
        // Kaf → keheh (U+06A9)
        'ك' => 'ک',   // U+0643 arabic kaf
        // Heh family → heh (U+0647)
        'ة' => 'ه',   // U+0629 teh marbuta
        'ھ' => 'ه',   // U+06BE heh doachashmee
        // Hamza carriers → bare letters
        'أ' => 'ا', 'إ' => 'ا', 'آ' => 'ا', 'ٱ' => 'ا',
        'ؤ' => 'و',
        'ئ' => 'ی',
    ];

    /**
     * Normalize inbound text. Safe for any field, including names.
     */
    public static function normalizeInput(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = strtr($value, self::EXTENDED_ARABIC_INDIC);
        $value = strtr($value, self::ARABIC_INDIC);
        $value = strtr($value, self::NUMERIC_PUNCTUATION);
        $value = str_replace(self::INVISIBLE, '', $value);

        // Collapse internal runs of whitespace and trim. Trailing whitespace is
        // a common source of phantom duplicate patients.
        $value = preg_replace('/\s+/u', ' ', $value);

        return trim($value);
    }

    /**
     * Build a search key. Write this to *_search columns only — never back over
     * the value the user entered.
     */
    public static function searchKey(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = self::normalizeInput($value);

        // Strip tatweel (decorative elongation) and harakat (optional
        // diacritics). Both are inconsistently typed and carry no identity.
        $value = preg_replace('/[\x{0640}\x{064B}-\x{065F}\x{0670}]/u', '', $value);

        // Strip zero-width joiners. Invisible, and inconsistently entered.
        $value = str_replace(["\u{200C}", "\u{200D}"], '', $value);

        $value = strtr($value, self::SEARCH_LETTER_FOLD);

        // mb_strtolower handles Latin; Arabic-script letters have no case.
        $value = mb_strtolower($value, 'UTF-8');

        return trim(preg_replace('/\s+/u', ' ', $value));
    }

    /**
     * Digits only, for phone numbers and national IDs.
     *
     * Note this deliberately does NOT validate. The e-Tazkira has no public
     * check-digit specification, so a guessed checksum would reject valid
     * patients (ADR-006). Format validation belongs in the request class.
     */
    public static function digitsOnly(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return preg_replace('/\D+/u', '', self::normalizeInput($value));
    }

    /**
     * Normalize an Afghan phone number to +93XXXXXXXXX where recognisable.
     * Returns the digit-normalized input unchanged if it does not match a
     * known shape — never guesses.
     */
    public static function phone(?string $value): ?string
    {
        $digits = self::digitsOnly($value);

        if ($digits === null || $digits === '') {
            return $digits;
        }

        // 0093XXXXXXXXX or 93XXXXXXXXX → +93XXXXXXXXX
        if (str_starts_with($digits, '0093')) {
            return '+93'.substr($digits, 4);
        }
        if (str_starts_with($digits, '93') && strlen($digits) === 11) {
            return '+93'.substr($digits, 2);
        }
        // Local 0XXXXXXXXX (10 digits) → +93XXXXXXXXX
        if (str_starts_with($digits, '0') && strlen($digits) === 10) {
            return '+93'.substr($digits, 1);
        }

        return $digits;
    }
}
