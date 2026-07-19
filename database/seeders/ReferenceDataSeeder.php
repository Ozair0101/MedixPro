<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Seeds permissions, roles, identifier types, units and adjustment reasons.
 *
 * Permissions marked is_sensitive force READ auditing. In this operating
 * environment the fact that someone LOOKED at a female patient's
 * reproductive-health record is the security event that matters, and a SELECT
 * fires no database trigger — so it has to be logged in the application
 * (ADR-010).
 */
class ReferenceDataSeeder extends Seeder
{
    /** [code, module, description, is_sensitive] */
    private const PERMISSIONS = [
        // Patient identity
        ['patient.read',            'identity',  'View patient demographics',            true],
        ['patient.create',          'identity',  'Register a new patient',               false],
        ['patient.update',          'identity',  'Amend patient demographics',           false],
        ['patient.merge',           'identity',  'Merge duplicate patient records',      true],
        ['patient.export',          'identity',  'Export patient data',                  true],
        ['patient.national_id.read', 'identity',  'View national identity numbers',       true],

        // Clinical
        ['encounter.read',          'clinical',  'View encounters',                      true],
        ['encounter.create',        'clinical',  'Open an encounter',                    false],
        ['observation.read',        'clinical',  'View clinical observations',           true],
        ['observation.create',      'clinical',  'Record clinical observations',         false],
        ['observation.amend',       'clinical',  'Amend a clinical observation',         true],
        ['diagnosis.record',        'clinical',  'Record a diagnosis',                   false],
        ['order.create',            'clinical',  'Place clinical orders',                false],
        ['order.discontinue',       'clinical',  'Discontinue an order',                 false],
        ['order.verbal',            'clinical',  'Record a verbal order',                true],
        ['note.sign',               'clinical',  'Electronically sign clinical notes',   true],
        // Reproductive-health data is genuinely dangerous data here. Separate
        // permission so it can be withheld independently of general clinical access.
        ['clinical.reproductive.read', 'clinical', 'View reproductive health records',   true],

        // Provider-gender override — must be logged with a reason every time.
        ['clinical.gender_override', 'clinical', 'Override same-gender provider rule',   true],

        // ADT
        ['admission.create',        'adt',       'Admit a patient',                      false],
        ['admission.transfer',      'adt',       'Transfer between beds or wards',       false],
        ['admission.discharge',     'adt',       'Discharge a patient',                  false],
        ['admission.cancel',        'adt',       'Cancel an admission or discharge',     true],

        // Pharmacy
        ['pharmacy.dispense',       'pharmacy',  'Dispense medication',                  false],
        ['pharmacy.dispense.override', 'pharmacy', 'Override an interaction or allergy alert', true],
        ['pharmacy.controlled',     'pharmacy',  'Handle controlled substances',         true],
        ['stock.receive',           'pharmacy',  'Receive stock',                        false],
        ['stock.adjust',            'pharmacy',  'Adjust stock levels',                  true],
        ['stock.count',             'pharmacy',  'Perform a stock count',                false],
        ['stock.transfer',          'pharmacy',  'Transfer stock between stores',        false],

        // Procurement
        ['purchase.create',         'procurement', 'Raise a purchase order',             false],
        ['purchase.approve',        'procurement', 'Approve a purchase order',           true],
        ['goods_receipt.create',    'procurement', 'Record goods received',              false],

        // Finance
        ['charge.create',           'finance',   'Capture a charge',                     false],
        ['invoice.issue',           'finance',   'Issue an invoice',                     false],
        ['invoice.cancel',          'finance',   'Cancel an invoice',                    true],
        ['payment.receive',         'finance',   'Receive a payment',                    false],
        ['payment.refund',          'finance',   'Approve a refund',                     true],
        ['adjustment.post',         'finance',   'Post an adjustment',                   false],
        ['adjustment.approve',      'finance',   'Approve a write-off',                  true],
        ['cashier.close_shift',     'finance',   'Close a cashier shift',                false],
        ['gl.post',                 'finance',   'Post to the general ledger',           true],
        ['gl.close_period',         'finance',   'Close an accounting period',           true],

        // Laboratory
        ['lab.collect',             'lab',       'Collect a specimen',                   false],
        ['lab.result.enter',        'lab',       'Enter a lab result',                   false],
        ['lab.result.validate',     'lab',       'Clinically validate a lab result',     true],

        // Reporting
        ['report.moph.generate',    'reporting', 'Generate MoPH reports',                false],
        ['report.moph.submit',      'reporting', 'Submit MoPH reports',                  true],
        ['report.export',           'reporting', 'Export report data',                   true],

        // Administration
        ['user.manage',             'admin',     'Manage users',                         true],
        ['role.manage',             'admin',     'Manage roles and permissions',         true],
        ['facility.manage',         'admin',     'Manage facility configuration',        true],
        ['audit.read',              'admin',     'View the audit trail',                 true],
        ['data.export_full',        'admin',     'Perform a full data export',           true],
    ];

    /** role code => [name, permission codes]. '*' means every permission. */
    private const ROLES = [
        'super_admin' => ['System Administrator', ['*']],
        'registration' => ['Registration Clerk', [
            'patient.read', 'patient.create', 'patient.update', 'encounter.create',
        ]],
        'doctor' => ['Doctor', [
            'patient.read', 'encounter.read', 'encounter.create',
            'observation.read', 'observation.create', 'observation.amend',
            'diagnosis.record', 'order.create', 'order.discontinue', 'note.sign',
            'admission.create', 'admission.discharge', 'lab.result.validate',
        ]],
        'nurse' => ['Nurse', [
            'patient.read', 'encounter.read', 'observation.read', 'observation.create',
            'order.verbal', 'admission.transfer', 'lab.collect',
        ]],
        'midwife' => ['Midwife', [
            'patient.read', 'encounter.read', 'encounter.create',
            'observation.read', 'observation.create', 'diagnosis.record',
            'clinical.reproductive.read', 'admission.create',
        ]],
        'pharmacist' => ['Pharmacist', [
            'patient.read', 'pharmacy.dispense', 'pharmacy.controlled',
            'stock.receive', 'stock.adjust', 'stock.count', 'stock.transfer',
            'goods_receipt.create',
        ]],
        'lab_tech' => ['Laboratory Technician', [
            'patient.read', 'lab.collect', 'lab.result.enter',
        ]],
        'cashier' => ['Cashier', [
            'patient.read', 'charge.create', 'invoice.issue',
            'payment.receive', 'cashier.close_shift',
        ]],
        'accountant' => ['Accountant', [
            'charge.create', 'invoice.issue', 'invoice.cancel', 'payment.receive',
            'payment.refund', 'adjustment.post', 'adjustment.approve',
            'gl.post', 'gl.close_period', 'report.export',
        ]],
        'records' => ['Medical Records Officer', [
            'patient.read', 'patient.merge', 'encounter.read',
            'report.moph.generate', 'report.moph.submit',
        ]],
        'store_keeper' => ['Store Keeper', [
            'stock.receive', 'stock.count', 'stock.transfer',
            'purchase.create', 'goods_receipt.create',
        ]],
    ];

    /** [code, name_local, name_latin, regex, unique] */
    private const IDENTIFIER_TYPES = [
        // 13 digits, formatted 0000-0000-00000. FORMAT ONLY: no public
        // check-digit specification exists, and a guessed checksum would
        // reject valid patients (ADR-006).
        ['e_tazkira',        'تذکره الکترونیکی', 'e-Tazkira',           '^[0-9]{13}$', true],
        // Paper tazkira uses jild/safha/sabt with no authoritative public
        // format — free text, unvalidated.
        ['paper_tazkira',    'تذکره کاغذی',      'Paper Tazkira',        null,          false],
        ['passport',         'پاسپورت',          'Passport',             null,          true],
        ['unhcr',            'راجستر UNHCR',     'UNHCR Registration',   null,          true],
        ['refugee_card',     'کارت مهاجر',       'Refugee Card',         null,          false],
        ['driving_licence',  'جواز رانندگی',     'Driving Licence',      null,          false],
    ];

    /** [code, kind, name_local, name_latin, requires_approval] */
    private const ADJUSTMENT_REASONS = [
        ['contract_rate',   'contractual',              'نرخ قرارداد',    'Contractual rate',      false],
        ['prompt_pay',      'discount',                 'تخفیف نقدی',     'Prompt payment discount', false],
        ['staff_discount',  'discount',                 'تخفیف کارمند',   'Staff discount',        true],
        ['charity',         'charity_care',             'خدمات خیریه',    'Charity care',          true],
        ['indigent',        'charity_care',             'بی‌بضاعت',       'Indigent patient',      true],
        ['uncollectable',   'bad_debt',                 'غیر قابل وصول',  'Uncollectable',         true],
        ['timely_filing',   'administrative_writeoff',  'مهلت گذشته',     'Timely filing lapse',   true],
        ['coding_error',    'administrative_writeoff',  'اشتباه کود',     'Coding error',          true],
        ['cash_rounding',   'rounding',                 'گردکردن',        'Cash rounding',         false],
    ];

    public function run(): void
    {
        DB::transaction(function () {
            $this->seedPermissions();
            $this->seedRoles();
            $this->seedIdentifierTypes();
            $this->seedAdjustmentReasons();
            $this->seedAgingBuckets();
            $this->seedUnits();
        });

        $this->command?->info(sprintf(
            'Reference data: %d permissions, %d roles, %d identifier types seeded.',
            count(self::PERMISSIONS),
            count(self::ROLES),
            count(self::IDENTIFIER_TYPES)
        ));
    }

    private function seedPermissions(): void
    {
        $rows = array_map(fn (array $p) => [
            'code' => $p[0],
            'module' => $p[1],
            'description' => $p[2],
            'is_sensitive' => $p[3],
        ], self::PERMISSIONS);

        DB::table('permission')->upsert($rows, ['code'], ['module', 'description', 'is_sensitive']);
    }

    private function seedRoles(): void
    {
        $allCodes = array_column(self::PERMISSIONS, 0);

        foreach (self::ROLES as $code => [$name, $permissions]) {
            // facility_id NULL = a system-wide role template. Facilities clone
            // these rather than each inventing their own.
            $existing = DB::table('role')
                ->whereNull('facility_id')->where('code', $code)->first();

            $roleId = $existing->id ?? (string) Str::uuid();

            if (! $existing) {
                DB::table('role')->insert([
                    'id' => $roleId,
                    'facility_id' => null,
                    'code' => $code,
                    'name' => $name,
                    'is_system' => true,
                ]);
            }

            $grant = $permissions === ['*'] ? $allCodes : $permissions;

            // Fail loudly on a typo rather than silently granting nothing.
            $unknown = array_diff($grant, $allCodes);
            if ($unknown !== []) {
                throw new \RuntimeException(sprintf(
                    "Role '%s' references unknown permissions: %s",
                    $code, implode(', ', $unknown)
                ));
            }

            // insertOrIgnore, not upsert: this is a pure join table with nothing
            // to update, and upsert() with an empty update list degrades to a
            // plain INSERT that collides on re-run.
            DB::table('role_permission')->insertOrIgnore(
                array_map(fn (string $p) => [
                    'role_id' => $roleId, 'permission_code' => $p,
                ], $grant)
            );
        }
    }

    private function seedIdentifierTypes(): void
    {
        $rows = array_map(fn (array $t) => [
            'code' => $t[0],
            'name_local' => $t[1],
            'name_latin' => $t[2],
            'validation_regex' => $t[3],
            'is_unique' => $t[4],
            'normalize_digits' => true,
        ], self::IDENTIFIER_TYPES);

        DB::table('identifier_type')->upsert(
            $rows,
            ['code'],
            ['name_local', 'name_latin', 'validation_regex', 'is_unique', 'normalize_digits']
        );
    }

    private function seedAdjustmentReasons(): void
    {
        $rows = array_map(fn (array $r) => [
            'code' => $r[0],
            'kind' => $r[1],
            'name_local' => $r[2],
            'name_latin' => $r[3],
            'requires_approval' => $r[4],
        ], self::ADJUSTMENT_REASONS);

        DB::table('adjustment_reason')->upsert(
            $rows, ['code'], ['kind', 'name_local', 'name_latin', 'requires_approval']
        );
    }

    private function seedAgingBuckets(): void
    {
        DB::table('aging_bucket')->upsert([
            ['id' => 1, 'label' => '0-30',   'days_gte' => 0,   'days_lt' => 31],
            ['id' => 2, 'label' => '31-60',  'days_gte' => 31,  'days_lt' => 61],
            ['id' => 3, 'label' => '61-90',  'days_gte' => 61,  'days_lt' => 91],
            ['id' => 4, 'label' => '91-120', 'days_gte' => 91,  'days_lt' => 121],
            ['id' => 5, 'label' => '120+',   'days_gte' => 121, 'days_lt' => null],
        ], ['id'], ['label', 'days_gte', 'days_lt']);
    }

    private function seedUnits(): void
    {
        $categories = [
            'mass' => [['mg', 'ملی‌گرام', 'milligram', 1, true],
                ['g',  'گرام',     'gram',      1000, false],
                ['mcg', 'مایکروگرام', 'microgram', 0.001, false]],
            'volume' => [['mL', 'ملی‌لیتر', 'millilitre', 1, true],
                ['L',  'لیتر',     'litre',      1000, false]],
            'count' => [['{tablet}',  'تابلیت', 'tablet',  1, true],
                ['{capsule}', 'کپسول',  'capsule', 1, false],
                ['{vial}',    'ویال',   'vial',    1, false],
                ['{ampoule}', 'امپول',  'ampoule', 1, false],
                ['{piece}',   'دانه',   'piece',   1, false],
                ['{box}',     'بکس',    'box',     1, false]],
        ];

        foreach ($categories as $categoryName => $units) {
            $cat = DB::table('uom_category')->where('name', $categoryName)->first();
            $catId = $cat->id ?? (string) Str::uuid();
            if (! $cat) {
                DB::table('uom_category')->insert(['id' => $catId, 'name' => $categoryName]);
            }

            DB::table('unit')->upsert(
                array_map(fn (array $u) => [
                    'id' => (string) Str::uuid(),
                    'uom_category_id' => $catId,
                    'code' => $u[0],
                    'name_local' => $u[1],
                    'name_latin' => $u[2],
                    'factor_to_reference' => $u[3],
                    'is_reference' => $u[4],
                ], $units),
                ['code'],
                ['name_local', 'name_latin', 'factor_to_reference']
            );
        }
    }
}
