<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Seeds the reference data every deployment needs before it can register a
 * single patient.
 *
 * Order matters — later seeders have foreign-key dependencies on earlier ones.
 *
 * NOTE ON THE LEGACY SEEDERS
 * --------------------------
 * UserSeeder, PharmacySeeder and InventorySeeder target the pre-migration
 * MySQL schema (`medications`, `prescriptions`, `categories`, `suppliers` with
 * an integer `hospital_id`). Those tables do not exist in the PostgreSQL
 * schema, so calling them here would fail. They are retained only to seed a
 * legacy database during migration testing, and are deliberately NOT in the
 * default path. See docs/04-migration-plan.md.
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            // 1. Geography first: facility rows reference province/district pcodes.
            GeographySeeder::class,

            // 2. Fiscal years and Shamsi months: accounting periods and MoPH
            //    reports both hang off these.
            CalendarSeeder::class,

            // 3. Permissions, roles, identifier types, units, adjustment
            //    reasons, aging buckets.
            ReferenceDataSeeder::class,
        ]);
    }
}
