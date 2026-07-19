<?php

namespace App\Console\Commands;

use App\Support\AfghanCalendar;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Provisions a new facility: the one command that makes an empty database
 * usable.
 *
 * Number series, org units, stock locations, roles and the chart of accounts
 * are all per-facility, so none of them can be seeded globally. Until a
 * facility exists, no patient can be registered — there is no MRN series to
 * draw from and no facility_id for Row-Level Security to scope against.
 *
 *   php artisan hmis:provision-facility \
 *       --name="شفاخانه ولایتی هرات" --name-latin="Herat Provincial Hospital" \
 *       --type=provincial_hospital --province=Herat --beds=150 --admin-gender=F
 *
 * --province accepts a p-code or a name. Prefer the name: p-codes are
 * deliberately meaningless identifiers and are easy to get wrong (AF11 is
 * Ghazni, not Herat), whereas the alias table resolves Herat -> AF32 (Hirat).
 */
class ProvisionFacility extends Command
{
    protected $signature = 'hmis:provision-facility
        {--name= : Facility name in Dari or Pashto}
        {--name-latin= : Facility name in Latin script}
        {--type=district_hospital : regional_hospital|provincial_hospital|district_hospital|specialised|chc|bhc|clinic}
        {--province= : Province p-code (AF32) or name (Herat / Hirat / هرات)}
        {--district= : District p-code (AF3201) or name}
        {--moph-code= : MoPH facility code, if already issued}
        {--beds=0 : Licensed bed count}
        {--admin-username=admin : Username for the first administrator}
        {--admin-password= : Password for the first administrator (generated if omitted)}
        {--admin-gender=M : M or F — required, it drives provider-gender matching}';

    protected $description = 'Create a facility with its number series, org units, stores, roles and chart of accounts';

    /** MoPH form type by facility tier. */
    private const FORM_TYPE = [
        'regional_hospital' => 'H1',
        'specialised' => 'H1',
        'provincial_hospital' => 'H2',
        'district_hospital' => 'H3',
    ];

    private const NUMBER_SERIES = [
        ['MRN',          'MRN-',  6, false],
        ['VISIT',        'V-',    8, true],
        ['ADMISSION',    'ADM-',  6, true],
        ['ENCOUNTER',    'E-',    8, true],
        ['ORDER',        'ORD-',  8, true],
        ['ACCESSION',    'LAB-',  7, true],
        ['IMAGING',      'IMG-',  7, true],
        ['ACCOUNT',      'ACC-',  6, false],
        // Invoices and credit notes must be GAPLESS for tax purposes and must
        // NOT reset mid-year without an explicit audit trail.
        ['INVOICE',      'INV-',  7, false],
        ['CREDIT_NOTE',  'CN-',   7, false],
        ['RECEIPT',      'RCT-',  7, false],
        ['DISPENSE',     'DSP-',  7, true],
        ['PO',           'PO-',   6, true],
        ['GRN',          'GRN-',  6, true],
        ['REQUISITION',  'REQ-',  6, true],
        ['TRANSFER',     'TRF-',  6, true],
        ['STOCK_COUNT',  'SC-',   5, true],
        ['INCIDENT',     'INC-',  5, true],
        ['COMPLAINT',    'CMP-',  5, true],
        ['EMPLOYEE',     'EMP-',  5, false],
    ];

    /** [code, name_local, name_latin, unit_type] */
    private const ORG_UNITS = [
        ['ADMIN',      'اداره',           'Administration',   'office'],
        ['OPD',        'کلینیک سرپایی',   'Outpatient',       'clinic'],
        ['ER',         'عاجل',            'Emergency',        'clinic'],
        ['IPD',        'بستر',            'Inpatient',        'department'],
        ['LAB',        'لابراتوار',       'Laboratory',       'lab'],
        ['RAD',        'رادیولوژی',       'Radiology',        'radiology'],
        ['PHARM',      'دواخانه',         'Pharmacy',         'pharmacy'],
        ['STORE',      'گدام',            'Main Store',       'store'],
        ['OT',         'اتاق عملیات',     'Operating Theatre', 'theatre'],
        ['MATERNITY',  'ولادی',           'Maternity',        'department'],
        ['FINANCE',    'مالی',            'Finance',          'cost_centre'],
        ['HR',         'منابع بشری',      'Human Resources',  'office'],
    ];

    /**
     * Expired, Destroyed and Transit are LOCATIONS, not statuses, so a
     * write-off is a transfer and the ledger identity still balances (ADR-013).
     */
    private const STOCK_LOCATIONS = [
        ['MAIN',      'گدام مرکزی',      'Main Store',       'internal',       true,  false],
        ['PHARM',     'دواخانه',         'Pharmacy',         'internal',       true,  true],
        ['WARD',      'ذخیره بخش',       'Ward Stock',       'internal',       true,  true],
        ['TRANSIT',   'در راه',          'In Transit',       'transit',        false, false],
        ['QUARANTINE', 'قرنطین',         'Quarantine',       'quarantine',     false, false],
        ['EXPIRED',   'منقضی',           'Expired',          'expired',        false, false],
        ['DESTROYED', 'معدوم شده',       'Destroyed',        'destroyed',      false, false],
        ['LOSS',      'ضایعات',          'Inventory Loss',   'inventory_loss', false, false],
    ];

    /** [code, name_local, name_latin, type, normal_side] */
    private const GL_ACCOUNTS = [
        ['1000', 'دارایی‌ها',              'Assets',                    'asset',          'DR'],
        ['1100', 'حسابات قابل دریافت - مریض', 'AR - Patient',            'asset',          'DR'],
        ['1110', 'حسابات قابل دریافت - بیمه', 'AR - Payer',              'asset',          'DR'],
        ['1200', 'صندوق',                   'Cash',                      'asset',          'DR'],
        ['1210', 'بانک',                    'Bank',                      'asset',          'DR'],
        ['1300', 'موجودی جنس',              'Inventory',                 'asset',          'DR'],
        ['2000', 'مکلفیت‌ها',               'Liabilities',               'liability',      'CR'],
        // Patient overpayments are a LIABILITY, never negative AR and never
        // revenue — under any overpayment rule they are money owed back.
        ['2100', 'امانت مریض',              'Patient Deposits',          'liability',      'CR'],
        ['2110', 'قابل پرداخت - بازپرداخت',  'Refunds Payable',           'liability',      'CR'],
        ['2200', 'مالیه قابل پرداخت',       'Tax Payable',               'liability',      'CR'],
        ['2210', 'مالیه موضوعی',            'Withholding Tax Payable',   'liability',      'CR'],
        ['3000', 'سرمایه',                  'Equity',                    'equity',         'CR'],
        ['4000', 'عواید خدمات صحی',         'Patient Service Revenue',   'revenue',        'CR'],
        // Contra-revenue, NOT expense. The distinction decides whether an
        // amount reduces revenue or becomes a credit loss.
        ['4100', 'تخفیفات',                 'Discounts',                 'contra_revenue', 'DR'],
        ['4110', 'خدمات خیریه',             'Charity Care',              'contra_revenue', 'DR'],
        ['4120', 'تخفیف قراردادی',          'Contractual Allowances',    'contra_revenue', 'DR'],
        ['5000', 'مصارف',                   'Expenses',                  'expense',        'DR'],
        ['5100', 'قرضه بد',                 'Bad Debt Expense',          'expense',        'DR'],
        ['5200', 'معاشات',                  'Salaries',                  'expense',        'DR'],
        ['5300', 'مصرف ادویه و مواد',       'Drugs and Consumables',     'expense',        'DR'],
    ];

    public function handle(): int
    {
        $nameLocal = $this->option('name') ?: $this->ask('Facility name (Dari/Pashto)');
        $nameLatin = $this->option('name-latin') ?: $this->ask('Facility name (Latin)');
        $type = $this->option('type');
        $gender = strtoupper($this->option('admin-gender'));

        if (! array_key_exists($type, self::FORM_TYPE) && ! in_array($type, ['chc', 'bhc', 'clinic'], true)) {
            $this->error("Unknown facility type '{$type}'.");

            return self::FAILURE;
        }

        if (! in_array($gender, ['M', 'F'], true)) {
            // Not cosmetic: provider gender drives patient assignment, ward
            // segregation and mahram rules (ADR-010).
            $this->error('--admin-gender must be M or F. It is clinically load-bearing.');

            return self::FAILURE;
        }

        if (DB::table('geo_province')->count() === 0) {
            $this->error('Geography is not seeded. Run: php artisan db:seed');

            return self::FAILURE;
        }

        $province = $this->resolveProvince($this->option('province'));
        $district = $this->resolveDistrict($this->option('district'), $province);

        if ($province === false || $district === false) {
            return self::FAILURE;
        }

        $password = $this->option('admin-password') ?: Str::password(16);
        $facilityId = (string) Str::uuid();

        DB::transaction(function () use (
            $facilityId, $nameLocal, $nameLatin, $type, $province, $district, $gender, $password
        ) {
            DB::table('facility')->insert([
                'id' => $facilityId,
                'moph_facility_code' => $this->option('moph-code'),
                'name_local' => $nameLocal,
                'name_latin' => $nameLatin,
                'facility_type' => $type,
                'moph_form_type' => self::FORM_TYPE[$type] ?? null,
                'province_pcode' => $province,
                'district_pcode' => $district,
                'licensed_beds' => (int) $this->option('beds'),
            ]);

            // Everything below is tenant-scoped, so RLS needs the context.
            DB::statement('SELECT set_config(?, ?, true)', ['app.facility_id', $facilityId]);

            $this->createNumberSeries($facilityId);
            $this->createOrgUnits($facilityId);
            $this->createStockLocations($facilityId);
            $this->createChartOfAccounts($facilityId);
            $this->createAccountingPeriods($facilityId);
            $this->createSettings($facilityId);
            $this->cloneRoles($facilityId);
            $this->createAdmin($facilityId, $gender, $password);
        });

        $this->newLine();
        $this->info("Facility provisioned: {$nameLatin}");
        $this->line("  id:       {$facilityId}");
        $this->line('  username: '.$this->option('admin-username'));

        if (! $this->option('admin-password')) {
            $this->newLine();
            $this->warn("  Generated admin password: {$password}");
            $this->warn('  Shown once. Store it now and change it on first login.');
        }

        return self::SUCCESS;
    }

    /**
     * Accept a p-code or a human name. P-codes are deliberately meaningless
     * identifiers — nobody memorises that Herat is AF32 — so falling back to
     * the alias table (which knows Herat/Hirat and Helmand/Hilmand) is what
     * makes this command usable without a lookup sheet.
     *
     * @return string|null|false p-code, null if not supplied, false on error
     */
    private function resolveProvince(?string $input): string|null|false
    {
        if (! $input) {
            return null;
        }

        if (DB::table('geo_province')->where('pcode', $input)->exists()) {
            return $input;
        }

        $matches = DB::table('geo_province')
            ->where('name_latin', 'ilike', $input)
            ->orWhere('name_dari', $input)
            ->orWhereIn('pcode', fn ($q) => $q->from('geo_alias')
                ->select('pcode')->whereRaw('lower(alias) = lower(?)', [$input]))
            ->get(['pcode', 'name_latin']);

        if ($matches->count() === 1) {
            $p = $matches->first();
            $this->line("  province: {$input} -> {$p->pcode} ({$p->name_latin})");

            return $p->pcode;
        }

        if ($matches->count() > 1) {
            $this->error("'{$input}' is ambiguous: "
                .$matches->map(fn ($m) => "{$m->pcode} {$m->name_latin}")->implode(', '));

            return false;
        }

        $this->error("Unknown province '{$input}'. Try a p-code (AF32) or a name (Herat).");

        return false;
    }

    private function resolveDistrict(?string $input, string|null|false $provincePcode): string|null|false
    {
        if (! $input || $provincePcode === false) {
            return $input ? false : null;
        }

        if (DB::table('geo_district')->where('pcode', $input)->exists()) {
            return $input;
        }

        $query = DB::table('geo_district')
            ->where(fn ($q) => $q->where('name_latin', 'ilike', $input)
                ->orWhere('name_dari', $input));

        // Scope to the province when we know it: district names repeat across
        // provinces far more often than province names do.
        if ($provincePcode !== null) {
            $query->where('province_pcode', $provincePcode);
        }

        $matches = $query->get(['pcode', 'name_latin']);

        if ($matches->count() === 1) {
            $d = $matches->first();
            $this->line("  district: {$input} -> {$d->pcode} ({$d->name_latin})");

            return $d->pcode;
        }

        if ($matches->count() > 1) {
            $this->error("District '{$input}' is ambiguous: "
                .$matches->map(fn ($m) => "{$m->pcode} {$m->name_latin}")->implode(', '));

            return false;
        }

        $this->error("Unknown district '{$input}'"
            .($provincePcode ? " in province {$provincePcode}." : '.'));

        return false;
    }

    private function createNumberSeries(string $facilityId): void
    {
        DB::table('number_series')->insert(array_map(fn (array $s) => [
            'id' => (string) Str::uuid(),
            'facility_id' => $facilityId,
            'series_code' => $s[0],
            'prefix' => $s[1],
            'padding' => $s[2],
            'resets_annually' => $s[3],
            'next_value' => 1,
        ], self::NUMBER_SERIES));
    }

    private function createOrgUnits(string $facilityId): void
    {
        DB::table('org_unit')->insert(array_map(fn (array $u) => [
            'id' => (string) Str::uuid(),
            'facility_id' => $facilityId,
            'code' => $u[0],
            'name_local' => $u[1],
            'name_latin' => $u[2],
            'unit_type' => $u[3],
            'is_cost_centre' => $u[3] === 'cost_centre',
        ], self::ORG_UNITS));
    }

    private function createStockLocations(string $facilityId): void
    {
        DB::table('stock_location')->insert(array_map(fn (array $l) => [
            'id' => (string) Str::uuid(),
            'facility_id' => $facilityId,
            'code' => $l[0],
            'name_local' => $l[1],
            'name_latin' => $l[2],
            'location_type' => $l[3],
            'counts_as_on_hand' => $l[4],
            'is_dispensing_point' => $l[5],
        ], self::STOCK_LOCATIONS));
    }

    private function createChartOfAccounts(string $facilityId): void
    {
        DB::table('gl_account')->insert(array_map(fn (array $a) => [
            'id' => (string) Str::uuid(),
            'facility_id' => $facilityId,
            'code' => $a[0],
            'name_local' => $a[1],
            'name_latin' => $a[2],
            'account_type' => $a[3],
            'normal_side' => $a[4],
            // Header accounts (round hundreds) are not postable; only leaves are.
            'is_postable' => ! str_ends_with($a[0], '000'),
        ], self::GL_ACCOUNTS));
    }

    /**
     * Open monthly accounting periods for the current fiscal year, aligned to
     * Shamsi month boundaries rather than Gregorian ones.
     */
    private function createAccountingPeriods(string $facilityId): void
    {
        $today = AfghanCalendar::toShamsi(now()->toDateTimeImmutable());

        $fiscalYear = DB::table('fiscal_year')->where('shamsi_year', $today['year'])->first();

        if (! $fiscalYear) {
            $this->warn("No fiscal year seeded for {$today['year']}; skipping accounting periods.");

            return;
        }

        $months = DB::table('shamsi_month')
            ->where('shamsi_year', $today['year'])
            ->orderBy('month_no')
            ->get();

        $rows = [];
        foreach ($months as $m) {
            $rows[] = [
                'id' => (string) Str::uuid(),
                'facility_id' => $facilityId,
                'fiscal_year_id' => $fiscalYear->id,
                // gregorian_period is already a half-open daterange, so adjacent
                // periods tile without overlapping.
                'period' => $m->gregorian_period,
                'status' => 'open',
            ];
        }

        DB::table('accounting_period')->insert($rows);
    }

    private function createSettings(string $facilityId): void
    {
        // Privacy defaults are protective out of the box; a facility opts out
        // deliberately rather than having to opt in (ADR-010).
        $settings = [
            ['privacy.hide_female_name_on_wristband', 'true', 'boolean'],
            ['privacy.queue_display_mode', '"token_only"', 'string'],
            ['scheduling.enforce_provider_gender', 'true', 'boolean'],
            ['sms.include_patient_name', 'false', 'boolean'],
            ['billing.currency', '"AFN"', 'string'],
            ['billing.cash_rounding_unit', '5', 'number'],
            ['billing.brt_rate', '0.02', 'number'],
            ['reporting.census_hour', '"23:59"', 'string'],
            ['locale.default', '"fa-AF"', 'string'],
        ];

        DB::table('app_setting')->insert(array_map(fn (array $s) => [
            'id' => (string) Str::uuid(),
            'facility_id' => $facilityId,
            'key' => $s[0],
            'value' => $s[1],
            'data_type' => $s[2],
        ], $settings));
    }

    /**
     * Copy the system role templates into this facility so local edits do not
     * mutate the shared templates.
     */
    private function cloneRoles(string $facilityId): void
    {
        $templates = DB::table('role')->whereNull('facility_id')->get();

        foreach ($templates as $template) {
            $roleId = (string) Str::uuid();

            DB::table('role')->insert([
                'id' => $roleId,
                'facility_id' => $facilityId,
                'code' => $template->code,
                'name' => $template->name,
                'description' => $template->description,
                'is_system' => false,
            ]);

            $permissions = DB::table('role_permission')
                ->where('role_id', $template->id)->pluck('permission_code');

            if ($permissions->isNotEmpty()) {
                DB::table('role_permission')->insert(
                    $permissions->map(fn ($p) => [
                        'role_id' => $roleId, 'permission_code' => $p,
                    ])->all()
                );
            }
        }
    }

    private function createAdmin(string $facilityId, string $gender, string $password): void
    {
        $userId = (string) Str::uuid();

        DB::table('app_user')->insert([
            'id' => $userId,
            'facility_id' => $facilityId,
            'username' => $this->option('admin-username'),
            'password_hash' => Hash::make($password),
            'display_name' => 'System Administrator',
            'gender' => $gender,
            'preferred_locale' => 'fa-AF',
        ]);

        $adminRole = DB::table('role')
            ->where('facility_id', $facilityId)->where('code', 'super_admin')->first();

        if ($adminRole) {
            DB::table('user_role')->insert([
                'user_id' => $userId,
                'role_id' => $adminRole->id,
            ]);
        }
    }
}
