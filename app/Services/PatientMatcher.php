<?php

namespace App\Services;

use App\Support\MedicalRecordNumber;
use App\Support\TextNormalizer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Search-before-create, and duplicate detection.
 *
 * This is the single highest-leverage safety control in the registration flow.
 * A duplicate patient record means a split chart: the allergy recorded last
 * month is invisible today, the running problem list is halved, and a repeat
 * prescription is dispensed against an empty history.
 *
 * WHY FUZZY MATCHING IS MANDATORY HERE
 * ------------------------------------
 * Exact matching does not work on Afghan names:
 *
 *  - A clerk on an Arabic keyboard types احمد ولي (yeh = U+064A); the patient
 *    was registered on an Afghan keyboard as احمد ولی (yeh = U+06CC). The
 *    strings are visually identical and byte-different. Handled by searching
 *    the normalized `name_search` column (ADR-007).
 *  - Transliteration is unstandardized: Massoud / Masoud / Masood. Handled by
 *    trigram similarity.
 *  - Honorifics drift in and out: "Haji Abdul Ghani" today, "Abdul Ghani"
 *    tomorrow. Handled by normalization stripping them into their own field.
 *
 * NOTHING HERE AUTO-MERGES. Candidates are scored and shown to a human. An
 * automatic merge on a false positive fuses two people's medical histories,
 * which is far more dangerous than the duplicate it was trying to prevent.
 */
class PatientMatcher
{
    /** Below this, a candidate is not worth a clerk's attention. */
    private const REVIEW_THRESHOLD = 0.45;

    /** At or above this, the records are near-certainly the same person. */
    private const STRONG_THRESHOLD = 0.80;

    /**
     * Free-text search across MRN, identifiers, phone and name.
     *
     * Deliberately ordered cheapest-and-most-exact first: a clerk who scans a
     * patient card should get an instant single hit, not a fuzzy name list.
     *
     * @return Collection<int, object>
     */
    public function search(string $term, int $limit = 20): Collection
    {
        $term = TextNormalizer::normalizeInput($term) ?? '';

        if (mb_strlen($term) < 2) {
            return collect();
        }

        // 1. An MRN, including one typed with Persian digits or no separators.
        if (MedicalRecordNumber::isValid($term)) {
            $exact = $this->byMrn(MedicalRecordNumber::normalize($term));

            if ($exact->isNotEmpty()) {
                return $exact;
            }
        }

        // 2. A national identifier or a phone number.
        $digits = TextNormalizer::digitsOnly($term);

        if ($digits !== null && strlen($digits) >= 6) {
            $byNumber = $this->byIdentifierOrPhone($digits);

            if ($byNumber->isNotEmpty()) {
                return $byNumber;
            }
        }

        // 3. Fall back to fuzzy name matching.
        return $this->byName($term, $limit);
    }

    /** @return Collection<int, object> */
    public function byMrn(?string $mrn): Collection
    {
        if ($mrn === null) {
            return collect();
        }

        return collect(DB::select(
            'SELECT p.id, p.mrn, pe.name_local, pe.name_latin, pe.father_name,
                    pe.gender, pe.birth_date, pe.village, 1.0 AS score,
                    ARRAY[\'mrn\'] AS matched_on
               FROM patient p
               JOIN person pe ON pe.id = p.id
              WHERE p.mrn = ? AND NOT pe.voided',
            [$mrn]
        ));
    }

    /** @return Collection<int, object> */
    public function byIdentifierOrPhone(string $digits): Collection
    {
        // Phones are matched on the SUBSCRIBER NUMBER — the last 9 digits.
        //
        // A number stored as +93701234567 has digits 93701234567, but a clerk
        // types the local form 0701234567. Neither is a prefix or suffix of the
        // other, because normalization drops the trunk '0' and adds the country
        // code. Comparing the trailing 9 digits makes every written form of the
        // same Afghan mobile number match: 0701234567, +93701234567,
        // 0093701234567 and 701234567 all share 701234567.
        $subscriber = strlen($digits) >= 9 ? substr($digits, -9) : null;

        return collect(DB::select(
            'SELECT DISTINCT p.id, p.mrn, pe.name_local, pe.name_latin,
                    pe.father_name, pe.gender, pe.birth_date, pe.village,
                    0.95 AS score, ARRAY[\'identifier\'] AS matched_on
               FROM patient p
               JOIN person pe ON pe.id = p.id
               LEFT JOIN patient_identifier pi
                      ON pi.patient_id = p.id AND NOT pi.voided
               LEFT JOIN patient_contact pc
                      ON pc.patient_id = p.id AND NOT pc.voided
              WHERE NOT pe.voided
                -- Identifiers match exactly: a 13-digit tazkira must never be
                -- matched on a partial, or two unrelated people collide.
                -- ::text casts are required: Postgres cannot infer the type of
                -- a bare placeholder in `? IS NOT NULL` when the value is null.
                AND (pi.value = ?::text
                     OR (?::text IS NOT NULL
                         AND right(regexp_replace(pc.value, \'\D\', \'\', \'g\'), 9) = ?::text))
              LIMIT 20',
            [$digits, $subscriber, $subscriber]
        ));
    }

    /**
     * Fuzzy name search over the normalized search column.
     *
     * pg_trgm similarity handles transliteration variance; the normalized
     * column handles the cross-script codepoint problem. Both are needed —
     * neither alone finds احمد ولي from احمد ولی.
     *
     * @return Collection<int, object>
     */
    public function byName(string $term, int $limit = 20): Collection
    {
        $key = TextNormalizer::searchKey($term);

        if ($key === null || $key === '') {
            return collect();
        }

        return collect(DB::select(
            'SELECT p.id, p.mrn, pe.name_local, pe.name_latin, pe.father_name,
                    pe.gender, pe.birth_date, pe.village,
                    GREATEST(
                        similarity(pe.name_search, ?),
                        similarity(COALESCE(lower(pe.name_latin), \'\'), ?)
                    ) AS score,
                    ARRAY[\'name\'] AS matched_on
               FROM patient p
               JOIN person pe ON pe.id = p.id
              WHERE NOT pe.voided
                AND (pe.name_search % ? OR lower(pe.name_latin) % ?)
              ORDER BY score DESC
              LIMIT ?',
            [$key, $key, $key, $key, $limit]
        ));
    }

    /**
     * Score potential duplicates of a candidate registration.
     *
     * The composite key is normalized given name + father's name + gender +
     * birth year within a tolerance + district. Father's name carries real
     * weight because, where most people have no surname, it is the primary
     * disambiguator in practice.
     *
     * @param  array<string, mixed>  $candidate
     * @return Collection<int, object>
     */
    public function findDuplicates(array $candidate): Collection
    {
        $nameKey = TextNormalizer::searchKey(
            trim(($candidate['name_local'] ?? '').' '.($candidate['father_name'] ?? ''))
        );

        if ($nameKey === null || $nameKey === '') {
            return collect();
        }

        $birthYear = isset($candidate['birth_date'])
            ? (int) date('Y', strtotime((string) $candidate['birth_date']))
            : null;

        $rows = DB::select(
            'SELECT p.id, p.mrn, pe.name_local, pe.name_latin, pe.father_name,
                    pe.gender, pe.birth_date, pe.village, pe.district_pcode,
                    similarity(pe.name_search, ?) AS name_score
               FROM patient p
               JOIN person pe ON pe.id = p.id
              WHERE NOT pe.voided
                AND p.is_active
                AND pe.name_search % ?
                -- Gender is a hard filter: two people of different recorded sex
                -- are not the same person, and a false positive here is worse
                -- than a missed duplicate.
                AND (pe.gender = ? OR pe.gender = \'U\' OR ? = \'U\')
              LIMIT 50',
            [$nameKey, $nameKey, $candidate['gender'] ?? 'U', $candidate['gender'] ?? 'U']
        );

        return collect($rows)
            ->map(function (object $row) use ($candidate, $birthYear) {
                [$score, $matchedOn] = $this->score($row, $candidate, $birthYear);
                $row->score = round($score, 4);
                $row->matched_on = $matchedOn;
                $row->confidence = $score >= self::STRONG_THRESHOLD ? 'strong' : 'possible';

                return $row;
            })
            ->filter(fn (object $row) => $row->score >= self::REVIEW_THRESHOLD)
            ->sortByDesc('score')
            ->values();
    }

    /**
     * Weighted score plus the reasons, so a reviewer can see WHY two records
     * were proposed as duplicates rather than being asked to trust a number.
     *
     * @param  array<string, mixed>  $candidate
     * @return array{0: float, 1: array<int, string>}
     */
    private function score(object $row, array $candidate, ?int $birthYear): array
    {
        $score = 0.0;
        $matched = [];

        // Name similarity dominates, but never decides alone.
        $nameScore = (float) $row->name_score;
        $score += $nameScore * 0.45;
        if ($nameScore > 0.6) {
            $matched[] = 'name';
        }

        // Father's name: the primary disambiguator where surnames are absent.
        $candidateFather = TextNormalizer::searchKey($candidate['father_name'] ?? '');
        $rowFather = TextNormalizer::searchKey($row->father_name ?? '');

        if ($candidateFather && $rowFather) {
            if ($candidateFather === $rowFather) {
                $score += 0.25;
                $matched[] = 'father_name';
            } elseif (similar_text($candidateFather, $rowFather) / max(mb_strlen($candidateFather), 1) > 0.7) {
                $score += 0.12;
            }
        }

        // Birth year within tolerance. Ages are frequently estimated, so an
        // exact match is not expected and a near miss is not disqualifying.
        if ($birthYear !== null && $row->birth_date) {
            $rowYear = (int) date('Y', strtotime((string) $row->birth_date));
            $delta = abs($rowYear - $birthYear);

            if ($delta === 0) {
                $score += 0.20;
                $matched[] = 'birth_date';
            } elseif ($delta <= 2) {
                $score += 0.10;
                $matched[] = 'birth_year_approx';
            }
        }

        // Same district: weak on its own, meaningful alongside a name match.
        if (! empty($candidate['district_pcode'])
            && $row->district_pcode === $candidate['district_pcode']) {
            $score += 0.10;
            $matched[] = 'district';
        }

        return [min($score, 1.0), $matched];
    }

    /**
     * Record candidates for human review.
     *
     * Written on registration so that duplicates surface in a work queue rather
     * than waiting to be noticed by whoever next opens the wrong chart.
     */
    public function recordCandidates(string $patientId, Collection $candidates): void
    {
        foreach ($candidates as $candidate) {
            // Defence in depth: a record is never its own duplicate. The
            // distinct_pair CHECK also rejects this, but failing silently here
            // is better than aborting a registration transaction.
            if ($candidate->id === $patientId) {
                continue;
            }

            // The unique constraint is on the ordered pair, so normalise the
            // ordering to avoid recording (A,B) and (B,A) as two open reviews.
            [$a, $b] = $patientId < $candidate->id
                ? [$patientId, $candidate->id]
                : [$candidate->id, $patientId];

            DB::table('patient_duplicate_candidate')->insertOrIgnore([
                'id' => (string) Str::uuid(),
                'facility_id' => DB::selectOne(
                    "SELECT nullif(current_setting('app.facility_id', true), '') AS f"
                )?->f,
                'patient_a_id' => $a,
                'patient_b_id' => $b,
                'score' => $candidate->score,
                'matched_on' => '{'.implode(',', $candidate->matched_on).'}',
                'status' => 'open',
            ]);
        }
    }
}
