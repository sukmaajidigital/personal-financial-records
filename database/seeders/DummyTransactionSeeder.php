<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DummyTransactionSeeder extends Seeder
{
    /**
     * Seed 1 full year of realistic transactions for user_id = 1.
     *
     * Creates standard expense/income categories (if not exist) and
     * generates daily transactions from 2025-03-01 to 2026-02-28.
     */
    public function run(): void
    {
        $userId = 1;

        // ── Standard categories with colors ──────────────────────────
        $categoryDefinitions = [
            // Income
            ['name' => 'Gaji',              'color' => '#10b981', 'type' => 'income'],
            ['name' => 'Freelance',          'color' => '#34d399', 'type' => 'income'],
            ['name' => 'Investasi',          'color' => '#3b82f6', 'type' => 'income'],
            // Expense
            ['name' => 'Makanan & Minuman',  'color' => '#f59e0b', 'type' => 'expense'],
            ['name' => 'Transportasi',       'color' => '#ef4444', 'type' => 'expense'],
            ['name' => 'Belanja',            'color' => '#8b5cf6', 'type' => 'expense'],
            ['name' => 'Hiburan',            'color' => '#ec4899', 'type' => 'expense'],
            ['name' => 'Tagihan',            'color' => '#f97316', 'type' => 'expense'],
            ['name' => 'Kesehatan',          'color' => '#06b6d4', 'type' => 'expense'],
            ['name' => 'Pendidikan',         'color' => '#6366f1', 'type' => 'expense'],
            ['name' => 'Rumah Tangga',       'color' => '#a855f7', 'type' => 'expense'],
            ['name' => 'Pakaian',            'color' => '#e11d48', 'type' => 'expense'],
            ['name' => 'Donasi',             'color' => '#14b8a6', 'type' => 'expense'],
        ];

        // Create or find categories
        $categories = [];
        foreach ($categoryDefinitions as $def) {
            $categories[$def['name']] = Category::firstOrCreate(
                ['user_id' => $userId, 'name' => $def['name']],
                ['color' => $def['color']]
            );
        }

        // Map category name → type
        $categoryTypes = collect($categoryDefinitions)->pluck('type', 'name')->toArray();

        // ── Transaction templates (realistic descriptions + amount ranges) ──
        $templates = [
            'Gaji' => [
                ['desc' => 'Gaji bulanan',              'min' => 5000000, 'max' => 8000000],
                ['desc' => 'Bonus kinerja',             'min' => 1000000, 'max' => 3000000],
                ['desc' => 'THR',                       'min' => 5000000, 'max' => 8000000],
            ],
            'Freelance' => [
                ['desc' => 'Project web development',   'min' => 1500000, 'max' => 5000000],
                ['desc' => 'Desain logo client',        'min' => 500000,  'max' => 2000000],
                ['desc' => 'Konsultasi IT',             'min' => 750000,  'max' => 2500000],
                ['desc' => 'Maintenance website',       'min' => 300000,  'max' => 1000000],
            ],
            'Investasi' => [
                ['desc' => 'Dividen saham',             'min' => 200000,  'max' => 1500000],
                ['desc' => 'Bunga deposito',            'min' => 100000,  'max' => 500000],
                ['desc' => 'Profit reksadana',          'min' => 150000,  'max' => 800000],
            ],
            'Makanan & Minuman' => [
                ['desc' => 'Makan siang',               'min' => 15000,  'max' => 50000],
                ['desc' => 'Sarapan pagi',               'min' => 10000,  'max' => 30000],
                ['desc' => 'Makan malam',                'min' => 20000,  'max' => 75000],
                ['desc' => 'Kopi & snack',               'min' => 15000,  'max' => 45000],
                ['desc' => 'Belanja groceries',          'min' => 100000, 'max' => 500000],
                ['desc' => 'Makan di restoran',          'min' => 50000,  'max' => 250000],
                ['desc' => 'Pesan GoFood/GrabFood',      'min' => 25000,  'max' => 80000],
                ['desc' => 'Beli air mineral',           'min' => 5000,   'max' => 20000],
            ],
            'Transportasi' => [
                ['desc' => 'Bensin motor',               'min' => 30000,  'max' => 80000],
                ['desc' => 'Grab/Gojek',                 'min' => 10000,  'max' => 50000],
                ['desc' => 'Parkir',                     'min' => 2000,   'max' => 15000],
                ['desc' => 'Tol',                        'min' => 10000,  'max' => 50000],
                ['desc' => 'Service kendaraan',          'min' => 100000, 'max' => 500000],
                ['desc' => 'KRL/MRT/TransJakarta',       'min' => 3000,   'max' => 15000],
            ],
            'Belanja' => [
                ['desc' => 'Belanja online Shopee',      'min' => 50000,  'max' => 500000],
                ['desc' => 'Belanja online Tokopedia',   'min' => 30000,  'max' => 400000],
                ['desc' => 'Beli peralatan rumah',       'min' => 50000,  'max' => 300000],
                ['desc' => 'Beli aksesoris gadget',      'min' => 50000,  'max' => 250000],
                ['desc' => 'Belanja di minimarket',      'min' => 20000,  'max' => 150000],
            ],
            'Hiburan' => [
                ['desc' => 'Nonton bioskop',             'min' => 35000,  'max' => 100000],
                ['desc' => 'Langganan Netflix',          'min' => 54000,  'max' => 186000],
                ['desc' => 'Langganan Spotify',          'min' => 54990,  'max' => 54990],
                ['desc' => 'Game mobile top-up',         'min' => 10000,  'max' => 150000],
                ['desc' => 'Karaoke',                    'min' => 50000,  'max' => 200000],
                ['desc' => 'Jalan-jalan weekend',        'min' => 100000, 'max' => 500000],
                ['desc' => 'Beli buku',                  'min' => 50000,  'max' => 150000],
            ],
            'Tagihan' => [
                ['desc' => 'Listrik PLN',                'min' => 150000, 'max' => 500000],
                ['desc' => 'Internet IndiHome/Biznet',   'min' => 200000, 'max' => 500000],
                ['desc' => 'Pulsa & paket data',         'min' => 50000,  'max' => 150000],
                ['desc' => 'Air PDAM',                   'min' => 50000,  'max' => 200000],
                ['desc' => 'Cicilan kredit',             'min' => 500000, 'max' => 2000000],
                ['desc' => 'Iuran BPJS',                'min' => 150000, 'max' => 300000],
                ['desc' => 'Sewa kos/kontrakan',         'min' => 1000000, 'max' => 3000000],
            ],
            'Kesehatan' => [
                ['desc' => 'Beli obat di apotek',        'min' => 20000,  'max' => 150000],
                ['desc' => 'Konsultasi dokter',          'min' => 100000, 'max' => 350000],
                ['desc' => 'Vitamin & suplemen',         'min' => 50000,  'max' => 200000],
                ['desc' => 'Medical check-up',           'min' => 300000, 'max' => 1000000],
                ['desc' => 'Perawatan gigi',             'min' => 150000, 'max' => 500000],
            ],
            'Pendidikan' => [
                ['desc' => 'Kursus online Udemy',        'min' => 100000, 'max' => 300000],
                ['desc' => 'Beli buku pelajaran',        'min' => 50000,  'max' => 200000],
                ['desc' => 'Langganan cloud/hosting',    'min' => 50000,  'max' => 200000],
                ['desc' => 'Workshop/seminar',           'min' => 100000, 'max' => 500000],
            ],
            'Rumah Tangga' => [
                ['desc' => 'Sabun & deterjen',           'min' => 20000,  'max' => 80000],
                ['desc' => 'Peralatan kebersihan',       'min' => 15000,  'max' => 100000],
                ['desc' => 'Gas LPG',                    'min' => 18000,  'max' => 35000],
                ['desc' => 'Beli perabotan kecil',       'min' => 30000,  'max' => 200000],
                ['desc' => 'Laundry',                    'min' => 15000,  'max' => 50000],
            ],
            'Pakaian' => [
                ['desc' => 'Beli baju baru',             'min' => 80000,  'max' => 400000],
                ['desc' => 'Beli sepatu',                'min' => 150000, 'max' => 600000],
                ['desc' => 'Beli celana',                'min' => 100000, 'max' => 350000],
                ['desc' => 'Cuci sepatu',                'min' => 25000,  'max' => 60000],
            ],
            'Donasi' => [
                ['desc' => 'Infaq Jumat',                'min' => 10000,  'max' => 50000],
                ['desc' => 'Donasi panti asuhan',        'min' => 50000,  'max' => 200000],
                ['desc' => 'Zakat',                      'min' => 200000, 'max' => 1000000],
                ['desc' => 'Sedekah',                    'min' => 10000,  'max' => 100000],
            ],
        ];

        // ── Generate transactions for 12 months ─────────────────────
        $startDate = Carbon::create(2025, 3, 1);
        $endDate = Carbon::create(2026, 2, 28);
        $rows = [];
        $now = now();

        for ($month = $startDate->copy(); $month->lte($endDate); $month->addMonth()) {
            $year = $month->year;
            $mon = $month->month;
            $daysInMonth = $month->daysInMonth;

            // ── 1) INCOME: Gaji on the 25th of each month ───────────
            $gajiTemplate = $templates['Gaji'][0]; // Gaji bulanan
            $rows[] = [
                'user_id' => $userId,
                'category_id' => $categories['Gaji']->id,
                'description' => $gajiTemplate['desc'],
                'amount' => $this->randomAmount($gajiTemplate['min'], $gajiTemplate['max']),
                'type' => 'income',
                'date' => Carbon::create($year, $mon, 25)->toDateString(),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            // Bonus kinerja every 3 months (March, June, Sept, Dec)
            if (in_array($mon, [3, 6, 9, 12])) {
                $bonusTemplate = $templates['Gaji'][1];
                $rows[] = [
                    'user_id' => $userId,
                    'category_id' => $categories['Gaji']->id,
                    'description' => $bonusTemplate['desc'],
                    'amount' => $this->randomAmount($bonusTemplate['min'], $bonusTemplate['max']),
                    'type' => 'income',
                    'date' => Carbon::create($year, $mon, 28)->toDateString(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // THR in June (Lebaran month)
            if ($mon === 6) {
                $thrTemplate = $templates['Gaji'][2];
                $rows[] = [
                    'user_id' => $userId,
                    'category_id' => $categories['Gaji']->id,
                    'description' => $thrTemplate['desc'],
                    'amount' => $this->randomAmount($thrTemplate['min'], $thrTemplate['max']),
                    'type' => 'income',
                    'date' => Carbon::create($year, $mon, 15)->toDateString(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // ── 2) INCOME: Freelance (1-3x per month randomly) ──────
            $freelanceCount = rand(0, 3);
            for ($i = 0; $i < $freelanceCount; $i++) {
                $tpl = $templates['Freelance'][array_rand($templates['Freelance'])];
                $rows[] = [
                    'user_id' => $userId,
                    'category_id' => $categories['Freelance']->id,
                    'description' => $tpl['desc'],
                    'amount' => $this->randomAmount($tpl['min'], $tpl['max']),
                    'type' => 'income',
                    'date' => Carbon::create($year, $mon, rand(1, $daysInMonth))->toDateString(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // ── 3) INCOME: Investasi (1x per month) ─────────────────
            $invTpl = $templates['Investasi'][array_rand($templates['Investasi'])];
            $rows[] = [
                'user_id' => $userId,
                'category_id' => $categories['Investasi']->id,
                'description' => $invTpl['desc'],
                'amount' => $this->randomAmount($invTpl['min'], $invTpl['max']),
                'type' => 'income',
                'date' => Carbon::create($year, $mon, rand(1, $daysInMonth))->toDateString(),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            // ── 4) EXPENSES: Makanan (daily, 1-3 transactions) ──────
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $mealCount = rand(1, 3);
                for ($i = 0; $i < $mealCount; $i++) {
                    $tpl = $templates['Makanan & Minuman'][array_rand($templates['Makanan & Minuman'])];
                    $rows[] = [
                        'user_id' => $userId,
                        'category_id' => $categories['Makanan & Minuman']->id,
                        'description' => $tpl['desc'],
                        'amount' => $this->randomAmount($tpl['min'], $tpl['max']),
                        'type' => 'expense',
                        'date' => Carbon::create($year, $mon, $day)->toDateString(),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            // ── 5) EXPENSES: Transportasi (15-25x per month) ────────
            $transportCount = rand(15, 25);
            for ($i = 0; $i < $transportCount; $i++) {
                $tpl = $templates['Transportasi'][array_rand($templates['Transportasi'])];
                $rows[] = [
                    'user_id' => $userId,
                    'category_id' => $categories['Transportasi']->id,
                    'description' => $tpl['desc'],
                    'amount' => $this->randomAmount($tpl['min'], $tpl['max']),
                    'type' => 'expense',
                    'date' => Carbon::create($year, $mon, rand(1, $daysInMonth))->toDateString(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // ── 6) EXPENSES: Tagihan (monthly bills) ────────────────
            foreach ($templates['Tagihan'] as $tpl) {
                $rows[] = [
                    'user_id' => $userId,
                    'category_id' => $categories['Tagihan']->id,
                    'description' => $tpl['desc'],
                    'amount' => $this->randomAmount($tpl['min'], $tpl['max']),
                    'type' => 'expense',
                    'date' => Carbon::create($year, $mon, rand(1, min(10, $daysInMonth)))->toDateString(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // ── 7) EXPENSES: Belanja (3-6x per month) ───────────────
            $belanjaCount = rand(3, 6);
            for ($i = 0; $i < $belanjaCount; $i++) {
                $tpl = $templates['Belanja'][array_rand($templates['Belanja'])];
                $rows[] = [
                    'user_id' => $userId,
                    'category_id' => $categories['Belanja']->id,
                    'description' => $tpl['desc'],
                    'amount' => $this->randomAmount($tpl['min'], $tpl['max']),
                    'type' => 'expense',
                    'date' => Carbon::create($year, $mon, rand(1, $daysInMonth))->toDateString(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // ── 8) EXPENSES: Hiburan (2-5x per month) ───────────────
            $hiburanCount = rand(2, 5);
            for ($i = 0; $i < $hiburanCount; $i++) {
                $tpl = $templates['Hiburan'][array_rand($templates['Hiburan'])];
                $rows[] = [
                    'user_id' => $userId,
                    'category_id' => $categories['Hiburan']->id,
                    'description' => $tpl['desc'],
                    'amount' => $this->randomAmount($tpl['min'], $tpl['max']),
                    'type' => 'expense',
                    'date' => Carbon::create($year, $mon, rand(1, $daysInMonth))->toDateString(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // ── 9) EXPENSES: Kesehatan (1-3x per month) ─────────────
            $kesehatanCount = rand(1, 3);
            for ($i = 0; $i < $kesehatanCount; $i++) {
                $tpl = $templates['Kesehatan'][array_rand($templates['Kesehatan'])];
                $rows[] = [
                    'user_id' => $userId,
                    'category_id' => $categories['Kesehatan']->id,
                    'description' => $tpl['desc'],
                    'amount' => $this->randomAmount($tpl['min'], $tpl['max']),
                    'type' => 'expense',
                    'date' => Carbon::create($year, $mon, rand(1, $daysInMonth))->toDateString(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // ── 10) EXPENSES: Pendidikan (1-2x per month) ───────────
            $pendidikanCount = rand(1, 2);
            for ($i = 0; $i < $pendidikanCount; $i++) {
                $tpl = $templates['Pendidikan'][array_rand($templates['Pendidikan'])];
                $rows[] = [
                    'user_id' => $userId,
                    'category_id' => $categories['Pendidikan']->id,
                    'description' => $tpl['desc'],
                    'amount' => $this->randomAmount($tpl['min'], $tpl['max']),
                    'type' => 'expense',
                    'date' => Carbon::create($year, $mon, rand(1, $daysInMonth))->toDateString(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // ── 11) EXPENSES: Rumah Tangga (3-5x per month) ─────────
            $rtCount = rand(3, 5);
            for ($i = 0; $i < $rtCount; $i++) {
                $tpl = $templates['Rumah Tangga'][array_rand($templates['Rumah Tangga'])];
                $rows[] = [
                    'user_id' => $userId,
                    'category_id' => $categories['Rumah Tangga']->id,
                    'description' => $tpl['desc'],
                    'amount' => $this->randomAmount($tpl['min'], $tpl['max']),
                    'type' => 'expense',
                    'date' => Carbon::create($year, $mon, rand(1, $daysInMonth))->toDateString(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // ── 12) EXPENSES: Pakaian (1-2x per month) ──────────────
            $pakaianCount = rand(0, 2);
            for ($i = 0; $i < $pakaianCount; $i++) {
                $tpl = $templates['Pakaian'][array_rand($templates['Pakaian'])];
                $rows[] = [
                    'user_id' => $userId,
                    'category_id' => $categories['Pakaian']->id,
                    'description' => $tpl['desc'],
                    'amount' => $this->randomAmount($tpl['min'], $tpl['max']),
                    'type' => 'expense',
                    'date' => Carbon::create($year, $mon, rand(1, $daysInMonth))->toDateString(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // ── 13) EXPENSES: Donasi (2-4x per month) ───────────────
            $donasiCount = rand(2, 4);
            for ($i = 0; $i < $donasiCount; $i++) {
                $tpl = $templates['Donasi'][array_rand($templates['Donasi'])];
                $rows[] = [
                    'user_id' => $userId,
                    'category_id' => $categories['Donasi']->id,
                    'description' => $tpl['desc'],
                    'amount' => $this->randomAmount($tpl['min'], $tpl['max']),
                    'type' => 'expense',
                    'date' => Carbon::create($year, $mon, rand(1, $daysInMonth))->toDateString(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // ── Bulk insert in chunks of 500 for performance ─────────────
        $chunks = array_chunk($rows, 500);
        foreach ($chunks as $chunk) {
            Transaction::insert($chunk);
        }

        $this->command->info('✅ Seeded '.count($rows)." transactions for user_id={$userId} (March 2025 – February 2026)");
    }

    /**
     * Generate a random amount with 2 decimal places.
     */
    private function randomAmount(int $min, int $max): float
    {
        return round(rand($min * 100, $max * 100) / 100, 2);
    }
}
