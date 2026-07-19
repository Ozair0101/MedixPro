<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Seeds Afghan administrative geography from the OCHA COD-AB gazetteer.
 *
 * Source: https://data.humdata.org/dataset/cod-ab-afg  (valid_on 2025-06-01)
 * Extracted to database/data/afghanistan-geo.json.
 *
 * 34 provinces, 401 ADM2 units (367 districts + 33 provincial centres + 1
 * capital). That 401 reconciles the conflicting public figures for "number of
 * districts in Afghanistan" (325 / 398 / 412 / 421) — use the gazetteer, not
 * a blog post.
 *
 * Reference data is VERSIONED (valid_on / valid_to) because provincial and
 * district boundaries genuinely change. This is also why facility codes must
 * never encode geography: the MoPH manual is explicit that facility codes are
 * deliberately meaningless identifiers for exactly this reason.
 *
 * Search aliases matter: the COD spells them Hirat, Hilmand, Panjsher, Nimroz.
 * Users type Herat, Helmand, Panjshir, Nimruz. Without aliases, registration
 * search silently returns nothing for four provinces.
 */
class GeographySeeder extends Seeder
{
    /**
     * Common transliteration variants users actually type.
     * COD spelling => alternates.
     */
    private const ALIASES = [
        'Hirat' => ['Herat'],
        'Hilmand' => ['Helmand'],
        'Panjsher' => ['Panjshir', 'Panjshér'],
        'Nimroz' => ['Nimruz', 'Nimrooz'],
        'Kunar' => ['Konar'],
        'Kunduz' => ['Kondoz', 'Qunduz'],
        'Uruzgan' => ['Urozgan', 'Oruzgan'],
        'Daykundi' => ['Daikundi', 'Day Kundi'],
        'Sar-e-Pul' => ['Sar-e Pol', 'Sari Pul'],
        'Jawzjan' => ['Jowzjan'],
        'Balkh' => ['Mazar-e Sharif'],
        'Nangarhar' => ['Ningarhar', 'Jalalabad'],
        'Paktya' => ['Paktia'],
        'Paktika' => ['Paktica'],
        'Maidan Wardak' => ['Wardak', 'Maydan Wardak', 'Maidan Shar'],
        'Kapisa' => ['Kapissa'],
        'Badghis' => ['Badghees'],
        'Ghazni' => ['Ghaznee'],
        'Zabul' => ['Zabol'],
        'Samangan' => ['Samanghan'],
        'Takhar' => ['Takhaar'],
        'Faryab' => ['Fariab'],
        'Ghor' => ['Ghowr', 'Ghour'],
        'Baghlan' => ['Baghlaan'],
        'Logar' => ['Lowgar'],
        'Khost' => ['Khowst'],
        'Nuristan' => ['Nooristan', 'Nurestan'],
        'Laghman' => ['Laghmaan'],
        'Parwan' => ['Parvan'],
        'Bamyan' => ['Bamiyan', 'Bamian'],
    ];

    public function run(): void
    {
        $path = database_path('data/afghanistan-geo.json');

        if (! is_file($path)) {
            throw new RuntimeException(
                "Geography data not found at {$path}. Regenerate it from the OCHA "
                .'COD-AB gazetteer: https://data.humdata.org/dataset/cod-ab-afg'
            );
        }

        $data = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        $validOn = $data['valid_on'];

        // Guard against a truncated or substituted data file. Seeding partial
        // reference data is worse than failing: districts would silently go
        // missing from registration dropdowns.
        if (count($data['provinces']) !== 34) {
            throw new RuntimeException(
                'Expected 34 provinces, found '.count($data['provinces'])
                .'. Refusing to seed partial reference data.'
            );
        }
        if (count($data['districts']) !== 401) {
            throw new RuntimeException(
                'Expected 401 ADM2 units, found '.count($data['districts'])
                .'. Refusing to seed partial reference data.'
            );
        }

        DB::transaction(function () use ($data, $validOn) {
            $provinces = array_map(fn (array $p) => [
                'pcode' => $p['pcode'],
                'name_latin' => $p['name_latin'],
                'name_dari' => $p['name_dari'],
                'name_pashto' => $p['name_pashto'],
                'region' => $p['region'],
                'valid_on' => $validOn,
            ], $data['provinces']);

            DB::table('geo_province')->upsert(
                $provinces,
                ['pcode'],
                ['name_latin', 'name_dari', 'name_pashto', 'region', 'valid_on']
            );

            $districts = array_map(fn (array $d) => [
                'pcode' => $d['pcode'],
                'province_pcode' => $d['province_pcode'],
                'name_latin' => $d['name_latin'],
                'name_dari' => $d['name_dari'],
                'name_pashto' => $d['name_pashto'],
                'valid_on' => $validOn,
            ], $data['districts']);

            foreach (array_chunk($districts, 200) as $chunk) {
                DB::table('geo_district')->upsert(
                    $chunk,
                    ['pcode'],
                    ['province_pcode', 'name_latin', 'name_dari', 'name_pashto', 'valid_on']
                );
            }

            $this->seedAliases($data['provinces']);
        });

        $this->command?->info(sprintf(
            'Geography: %d provinces, %d districts seeded (COD-AB valid_on %s).',
            count($data['provinces']),
            count($data['districts']),
            $validOn
        ));
    }

    /**
     * @param  array<int, array<string, mixed>>  $provinces
     */
    private function seedAliases(array $provinces): void
    {
        $byName = [];
        foreach ($provinces as $p) {
            $byName[$p['name_latin']] = $p['pcode'];
        }

        $rows = [];
        foreach (self::ALIASES as $codName => $alternates) {
            if (! isset($byName[$codName])) {
                // The COD spelling changed; the alias list needs updating rather
                // than silently doing nothing.
                $this->command?->warn(
                    "Alias target '{$codName}' not found in the gazetteer — skipping."
                );

                continue;
            }
            foreach ($alternates as $alias) {
                $rows[] = ['pcode' => $byName[$codName], 'alias' => $alias];
            }
        }

        // Every province is also an alias of itself, so one query serves both
        // exact and fuzzy lookups.
        foreach ($byName as $name => $pcode) {
            $rows[] = ['pcode' => $pcode, 'alias' => $name];
        }

        DB::table('geo_alias')->upsert($rows, ['pcode', 'alias'], ['alias']);
    }
}
