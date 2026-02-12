<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { dashboard, login, register } from '@/routes';
import {
    ArrowDownRight,
    ArrowLeftRight,
    ArrowUpRight,
    LayoutGrid,
    Lock,
    Palette,
    Shield,
    Tag,
    TrendingUp,
    User,
    Wallet,
} from 'lucide-vue-next';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);

// Dummy data - not from database
const summaryCards = [
    {
        label: 'Saldo',
        value: 'Rp 12.450.000',
        change: '+8.2%',
        up: true,
        icon: Wallet,
    },
    {
        label: 'Pemasukan',
        value: 'Rp 18.500.000',
        change: '+12.5%',
        up: true,
        icon: ArrowUpRight,
    },
    {
        label: 'Pengeluaran',
        value: 'Rp 6.050.000',
        change: '-3.1%',
        up: false,
        icon: ArrowDownRight,
    },
    {
        label: 'Transaksi',
        value: '47',
        change: '+5',
        up: true,
        icon: ArrowLeftRight,
    },
];

const monthlyTrends = [
    { month: 'Sep', income: 14200000, expense: 5800000 },
    { month: 'Okt', income: 15800000, expense: 6200000 },
    { month: 'Nov', income: 16500000, expense: 7100000 },
    { month: 'Des', income: 19000000, expense: 8500000 },
    { month: 'Jan', income: 17200000, expense: 5500000 },
    { month: 'Feb', income: 18500000, expense: 6050000 },
];

const maxAmount = Math.max(
    ...monthlyTrends.map((t) => Math.max(t.income, t.expense)),
);

const categories = [
    {
        name: 'Makanan & Minuman',
        color: '#EF4444',
        amount: 1850000,
        percentage: 30,
    },
    { name: 'Transportasi', color: '#3B82F6', amount: 1200000, percentage: 20 },
    { name: 'Belanja', color: '#F59E0B', amount: 950000, percentage: 16 },
    {
        name: 'Tagihan & Utilitas',
        color: '#8B5CF6',
        amount: 850000,
        percentage: 14,
    },
    { name: 'Hiburan', color: '#EC4899', amount: 650000, percentage: 11 },
    { name: 'Lainnya', color: '#6B7280', amount: 550000, percentage: 9 },
];

const recentTransactions = [
    {
        date: '13 Feb 2026',
        description: 'Gaji Bulanan',
        category: 'Gaji',
        type: 'income' as const,
        amount: 15000000,
        color: '#22C55E',
    },
    {
        date: '12 Feb 2026',
        description: 'Belanja Groceries',
        category: 'Makanan & Minuman',
        type: 'expense' as const,
        amount: 450000,
        color: '#EF4444',
    },
    {
        date: '11 Feb 2026',
        description: 'Grab Transport',
        category: 'Transportasi',
        type: 'expense' as const,
        amount: 85000,
        color: '#3B82F6',
    },
    {
        date: '10 Feb 2026',
        description: 'Freelance Project',
        category: 'Gaji',
        type: 'income' as const,
        amount: 3500000,
        color: '#22C55E',
    },
    {
        date: '09 Feb 2026',
        description: 'Listrik & Air',
        category: 'Tagihan & Utilitas',
        type: 'expense' as const,
        amount: 650000,
        color: '#8B5CF6',
    },
];

const allCategories = [
    { name: 'Gaji', color: '#22C55E', transactions: 4 },
    { name: 'Makanan & Minuman', color: '#EF4444', transactions: 12 },
    { name: 'Transportasi', color: '#3B82F6', transactions: 8 },
    { name: 'Belanja', color: '#F59E0B', transactions: 6 },
    { name: 'Tagihan & Utilitas', color: '#8B5CF6', transactions: 3 },
    { name: 'Hiburan', color: '#EC4899', transactions: 5 },
    { name: 'Investasi', color: '#14B8A6', transactions: 2 },
    { name: 'Lainnya', color: '#6B7280', transactions: 7 },
];

const features = [
    {
        icon: LayoutGrid,
        title: 'Dashboard Interaktif',
        desc: 'Lihat ringkasan keuangan, tren bulanan, dan distribusi pengeluaran per kategori dalam satu tampilan.',
    },
    {
        icon: ArrowLeftRight,
        title: 'Manajemen Transaksi',
        desc: 'Catat pemasukan & pengeluaran dengan mudah. Filter berdasarkan tipe, kategori, tanggal, dan pencarian.',
    },
    {
        icon: Tag,
        title: 'Kategori Kustom',
        desc: 'Buat dan kelola kategori dengan warna pilihan untuk mengelompokkan transaksi Anda.',
    },
    {
        icon: Shield,
        title: 'Keamanan 2FA',
        desc: 'Lindungi akun Anda dengan Two-Factor Authentication menggunakan aplikasi authenticator.',
    },
];

const settingsMenu = [
    {
        icon: User,
        title: 'Profil',
        desc: 'Ubah nama, email, dan informasi akun Anda.',
    },
    {
        icon: Lock,
        title: 'Password',
        desc: 'Perbarui password untuk menjaga keamanan akun.',
    },
    {
        icon: Palette,
        title: 'Tampilan',
        desc: 'Pilih tema terang, gelap, atau ikuti sistem.',
    },
    {
        icon: Shield,
        title: 'Two-Factor Auth',
        desc: 'Aktifkan verifikasi dua langkah untuk keamanan ekstra.',
    },
];

function formatCurrency(n: number): string {
    return 'Rp ' + n.toLocaleString('id-ID');
}
</script>

<template>
    <Head title="Personal Financial Records">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <div
        class="min-h-screen bg-gradient-to-b from-gray-50 to-white dark:from-[#0a0a0a] dark:to-[#161615]"
    >
        <!-- Header -->
        <header
            class="sticky top-0 z-50 border-b border-gray-200/60 bg-white/80 backdrop-blur-lg dark:border-[#2E2E2A]/60 dark:bg-[#0a0a0a]/80"
        >
            <div
                class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8"
            >
                <div class="flex items-center gap-3">
                    <img src="/logo/finsu.png" alt="Finsu" class="h-12 w-12" />
                </div>
                <nav class="flex items-center gap-3">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="dashboard()"
                        class="rounded-lg bg-emerald-600 px-5 py-2 text-sm font-medium text-white transition hover:bg-emerald-700"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="login()"
                            class="rounded-lg px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100 dark:text-[#EDEDEC] dark:hover:bg-[#2E2E2A]"
                        >
                            Masuk
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="register()"
                            class="rounded-lg bg-emerald-600 px-5 py-2 text-sm font-medium text-white transition hover:bg-emerald-700"
                        >
                            Daftar
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <!-- Hero -->
        <section
            class="mx-auto max-w-7xl px-4 py-16 text-center sm:px-6 sm:py-24 lg:px-8"
        >
            <div class="mx-auto max-w-3xl">
                <span
                    class="mb-4 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-4 py-1.5 text-sm font-medium text-emerald-700 dark:border-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400"
                >
                    <TrendingUp class="h-4 w-4" />
                    Kelola Keuangan Pribadi Anda
                </span>
                <h1
                    class="mt-6 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl lg:text-6xl dark:text-[#EDEDEC]"
                >
                    Personal Financial
                    <span class="text-emerald-600">Records</span>
                </h1>
                <p
                    class="mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-gray-600 dark:text-[#A1A09A]"
                >
                    Aplikasi pencatatan keuangan pribadi yang membantu Anda
                    melacak pemasukan, pengeluaran, dan menganalisis pola
                    keuangan Anda secara visual dan terorganisir.
                </p>
                <div
                    class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row"
                >
                    <Link
                        v-if="!$page.props.auth.user"
                        :href="register()"
                        class="w-full rounded-lg bg-emerald-600 px-8 py-3 text-base font-semibold text-white shadow-lg shadow-emerald-500/25 transition hover:bg-emerald-700 sm:w-auto"
                    >
                        Mulai Sekarang Gratis
                    </Link>
                    <a
                        href="#preview"
                        class="w-full rounded-lg border border-gray-300 px-8 py-3 text-base font-semibold text-gray-700 transition hover:bg-gray-50 sm:w-auto dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:bg-[#1C1C1A]"
                    >
                        Lihat Preview
                    </a>
                </div>
            </div>
        </section>

        <!-- Features Grid -->
        <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="feature in features"
                    :key="feature.title"
                    class="group rounded-xl border border-gray-200 bg-white p-6 transition hover:border-emerald-300 hover:shadow-lg hover:shadow-emerald-500/5 dark:border-[#2E2E2A] dark:bg-[#161615] dark:hover:border-emerald-700"
                >
                    <div
                        class="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 transition group-hover:bg-emerald-100 dark:bg-emerald-900/20 dark:group-hover:bg-emerald-900/40"
                    >
                        <component :is="feature.icon" class="h-5 w-5" />
                    </div>
                    <h3
                        class="mb-2 font-semibold text-gray-900 dark:text-[#EDEDEC]"
                    >
                        {{ feature.title }}
                    </h3>
                    <p
                        class="text-sm leading-relaxed text-gray-500 dark:text-[#A1A09A]"
                    >
                        {{ feature.desc }}
                    </p>
                </div>
            </div>
        </section>

        <!-- Preview: Dashboard -->
        <section
            id="preview"
            class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8"
        >
            <div class="mb-10 text-center">
                <h2
                    class="text-2xl font-bold text-gray-900 sm:text-3xl dark:text-[#EDEDEC]"
                >
                    Preview Tampilan
                </h2>
                <p class="mt-2 text-gray-500 dark:text-[#A1A09A]">
                    Berikut contoh tampilan dari setiap fitur yang tersedia
                    (data dummy)
                </p>
            </div>

            <!-- Dashboard Preview -->
            <div class="mb-12">
                <div class="mb-4 flex items-center gap-2">
                    <LayoutGrid class="h-5 w-5 text-emerald-600" />
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-[#EDEDEC]"
                    >
                        Dashboard
                    </h3>
                </div>
                <div
                    class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-[#2E2E2A] dark:bg-[#161615]"
                >
                    <!-- Summary Cards -->
                    <div
                        class="grid grid-cols-2 gap-px border-b border-gray-200 bg-gray-200 lg:grid-cols-4 dark:border-[#2E2E2A] dark:bg-[#2E2E2A]"
                    >
                        <div
                            v-for="card in summaryCards"
                            :key="card.label"
                            class="bg-white p-4 sm:p-6 dark:bg-[#161615]"
                        >
                            <div class="flex items-center justify-between">
                                <span
                                    class="text-xs font-medium text-gray-500 sm:text-sm dark:text-[#A1A09A]"
                                    >{{ card.label }}</span
                                >
                                <component
                                    :is="card.icon"
                                    class="hidden h-4 w-4 text-gray-400 sm:block"
                                />
                            </div>
                            <p
                                class="mt-2 text-lg font-bold text-gray-900 sm:text-2xl dark:text-[#EDEDEC]"
                            >
                                {{ card.value }}
                            </p>
                            <span
                                class="mt-1 inline-block text-xs font-medium"
                                :class="
                                    card.up
                                        ? 'text-emerald-600'
                                        : 'text-red-500'
                                "
                            >
                                {{ card.change }} dari bulan lalu
                            </span>
                        </div>
                    </div>

                    <div
                        class="grid gap-px border-b border-gray-200 bg-gray-200 lg:grid-cols-2 dark:border-[#2E2E2A] dark:bg-[#2E2E2A]"
                    >
                        <!-- Monthly Trend -->
                        <div class="bg-white p-4 sm:p-6 dark:bg-[#161615]">
                            <h4
                                class="mb-4 text-sm font-semibold text-gray-900 dark:text-[#EDEDEC]"
                            >
                                Tren 6 Bulan Terakhir
                            </h4>
                            <div
                                class="flex items-end gap-2 sm:gap-3"
                                style="height: 140px"
                            >
                                <div
                                    v-for="item in monthlyTrends"
                                    :key="item.month"
                                    class="flex flex-1 flex-col items-center gap-1"
                                >
                                    <div class="flex w-full gap-0.5">
                                        <div
                                            class="w-1/2 rounded-t bg-emerald-400 transition-all dark:bg-emerald-500"
                                            :style="{
                                                height:
                                                    (item.income / maxAmount) *
                                                        100 +
                                                    'px',
                                            }"
                                        />
                                        <div
                                            class="w-1/2 rounded-t bg-red-300 transition-all dark:bg-red-500"
                                            :style="{
                                                height:
                                                    (item.expense / maxAmount) *
                                                        100 +
                                                    'px',
                                            }"
                                        />
                                    </div>
                                    <span
                                        class="text-[10px] text-gray-500 dark:text-[#A1A09A]"
                                        >{{ item.month }}</span
                                    >
                                </div>
                            </div>
                            <div
                                class="mt-3 flex items-center gap-4 text-xs text-gray-500 dark:text-[#A1A09A]"
                            >
                                <span class="flex items-center gap-1"
                                    ><span
                                        class="h-2 w-2 rounded-full bg-emerald-400"
                                    />
                                    Pemasukan</span
                                >
                                <span class="flex items-center gap-1"
                                    ><span
                                        class="h-2 w-2 rounded-full bg-red-300"
                                    />
                                    Pengeluaran</span
                                >
                            </div>
                        </div>

                        <!-- Expense by Category -->
                        <div class="bg-white p-4 sm:p-6 dark:bg-[#161615]">
                            <h4
                                class="mb-4 text-sm font-semibold text-gray-900 dark:text-[#EDEDEC]"
                            >
                                Pengeluaran per Kategori
                            </h4>
                            <div class="space-y-3">
                                <div v-for="cat in categories" :key="cat.name">
                                    <div
                                        class="mb-1 flex items-center justify-between text-xs"
                                    >
                                        <span
                                            class="flex items-center gap-2 text-gray-700 dark:text-[#EDEDEC]"
                                        >
                                            <span
                                                class="h-2.5 w-2.5 rounded-full"
                                                :style="{
                                                    backgroundColor: cat.color,
                                                }"
                                            />
                                            {{ cat.name }}
                                        </span>
                                        <span
                                            class="text-gray-500 dark:text-[#A1A09A]"
                                            >{{
                                                formatCurrency(cat.amount)
                                            }}</span
                                        >
                                    </div>
                                    <div
                                        class="h-1.5 overflow-hidden rounded-full bg-gray-100 dark:bg-[#2E2E2A]"
                                    >
                                        <div
                                            class="h-full rounded-full transition-all"
                                            :style="{
                                                width: cat.percentage + '%',
                                                backgroundColor: cat.color,
                                            }"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Transactions Preview -->
                    <div class="p-4 sm:p-6">
                        <h4
                            class="mb-3 text-sm font-semibold text-gray-900 dark:text-[#EDEDEC]"
                        >
                            Transaksi Terakhir
                        </h4>
                        <div class="space-y-2">
                            <div
                                v-for="tx in recentTransactions"
                                :key="tx.description"
                                class="flex items-center justify-between rounded-lg px-3 py-2 transition hover:bg-gray-50 dark:hover:bg-[#1C1C1A]"
                            >
                                <div class="flex items-center gap-3">
                                    <span
                                        class="hidden h-8 w-8 items-center justify-center rounded-full sm:flex"
                                        :style="{
                                            backgroundColor: tx.color + '15',
                                        }"
                                    >
                                        <ArrowUpRight
                                            v-if="tx.type === 'income'"
                                            class="h-4 w-4"
                                            :style="{ color: tx.color }"
                                        />
                                        <ArrowDownRight
                                            v-else
                                            class="h-4 w-4"
                                            :style="{ color: tx.color }"
                                        />
                                    </span>
                                    <div>
                                        <p
                                            class="text-sm font-medium text-gray-900 dark:text-[#EDEDEC]"
                                        >
                                            {{ tx.description }}
                                        </p>
                                        <p
                                            class="text-xs text-gray-500 dark:text-[#A1A09A]"
                                        >
                                            {{ tx.category }} {{ tx.date }}
                                        </p>
                                    </div>
                                </div>
                                <span
                                    class="text-sm font-semibold"
                                    :class="
                                        tx.type === 'income'
                                            ? 'text-emerald-600'
                                            : 'text-red-500'
                                    "
                                >
                                    {{ tx.type === 'income' ? '+' : '-'
                                    }}{{ formatCurrency(tx.amount) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transactions Preview -->
            <div class="mb-12">
                <div class="mb-4 flex items-center gap-2">
                    <ArrowLeftRight class="h-5 w-5 text-emerald-600" />
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-[#EDEDEC]"
                    >
                        Transaksi
                    </h3>
                </div>
                <div
                    class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-[#2E2E2A] dark:bg-[#161615]"
                >
                    <!-- Filters Bar -->
                    <div
                        class="flex flex-wrap items-center gap-3 border-b border-gray-200 p-4 dark:border-[#2E2E2A]"
                    >
                        <div
                            class="flex h-9 flex-1 items-center rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm text-gray-400 dark:border-[#3E3E3A] dark:bg-[#1C1C1A] dark:text-[#A1A09A]"
                        >
                            Cari transaksi...
                        </div>
                        <div
                            class="flex h-9 items-center rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm text-gray-500 dark:border-[#3E3E3A] dark:bg-[#1C1C1A] dark:text-[#A1A09A]"
                        >
                            Semua Tipe
                        </div>
                        <div
                            class="flex h-9 items-center rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm text-gray-500 dark:border-[#3E3E3A] dark:bg-[#1C1C1A] dark:text-[#A1A09A]"
                        >
                            Semua Kategori
                        </div>
                        <button
                            class="h-9 rounded-lg bg-emerald-600 px-4 text-sm font-medium text-white"
                        >
                            + Tambah
                        </button>
                    </div>
                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr
                                    class="border-b border-gray-100 text-left text-xs font-medium text-gray-500 dark:border-[#2E2E2A] dark:text-[#A1A09A]"
                                >
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="px-4 py-3">Deskripsi</th>
                                    <th class="px-4 py-3">Kategori</th>
                                    <th class="px-4 py-3">Tipe</th>
                                    <th class="px-4 py-3 text-right">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody
                                class="divide-y divide-gray-100 dark:divide-[#2E2E2A]"
                            >
                                <tr
                                    v-for="tx in recentTransactions"
                                    :key="tx.description + tx.date"
                                    class="transition hover:bg-gray-50 dark:hover:bg-[#1C1C1A]"
                                >
                                    <td
                                        class="px-4 py-3 whitespace-nowrap text-gray-500 dark:text-[#A1A09A]"
                                    >
                                        {{ tx.date }}
                                    </td>
                                    <td
                                        class="px-4 py-3 font-medium text-gray-900 dark:text-[#EDEDEC]"
                                    >
                                        {{ tx.description }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="inline-flex items-center gap-1.5 text-gray-600 dark:text-[#EDEDEC]"
                                        >
                                            <span
                                                class="h-2 w-2 rounded-full"
                                                :style="{
                                                    backgroundColor: tx.color,
                                                }"
                                            />
                                            {{ tx.category }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                            :class="
                                                tx.type === 'income'
                                                    ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
                                                    : 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400'
                                            "
                                        >
                                            {{
                                                tx.type === 'income'
                                                    ? 'Pemasukan'
                                                    : 'Pengeluaran'
                                            }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-4 py-3 text-right font-semibold whitespace-nowrap"
                                        :class="
                                            tx.type === 'income'
                                                ? 'text-emerald-600'
                                                : 'text-red-500'
                                        "
                                    >
                                        {{ tx.type === 'income' ? '+' : '-'
                                        }}{{ formatCurrency(tx.amount) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div
                        class="flex items-center justify-between border-t border-gray-200 px-4 py-3 text-xs text-gray-500 dark:border-[#2E2E2A] dark:text-[#A1A09A]"
                    >
                        <span>Menampilkan 1-5 dari 47 transaksi</span>
                        <div class="flex gap-1">
                            <span
                                class="flex h-7 w-7 items-center justify-center rounded bg-emerald-600 text-xs font-medium text-white"
                                >1</span
                            >
                            <span
                                class="flex h-7 w-7 items-center justify-center rounded text-gray-500 transition hover:bg-gray-100 dark:hover:bg-[#2E2E2A]"
                                >2</span
                            >
                            <span
                                class="flex h-7 w-7 items-center justify-center rounded text-gray-500 transition hover:bg-gray-100 dark:hover:bg-[#2E2E2A]"
                                >3</span
                            >
                            <span class="text-gray-400">...</span>
                            <span
                                class="flex h-7 w-7 items-center justify-center rounded text-gray-500 transition hover:bg-gray-100 dark:hover:bg-[#2E2E2A]"
                                >10</span
                            >
                        </div>
                    </div>
                </div>
            </div>

            <!-- Categories Preview -->
            <div class="mb-12">
                <div class="mb-4 flex items-center gap-2">
                    <Tag class="h-5 w-5 text-emerald-600" />
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-[#EDEDEC]"
                    >
                        Kategori
                    </h3>
                </div>
                <div
                    class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-[#2E2E2A] dark:bg-[#161615]"
                >
                    <div
                        class="flex items-center justify-between border-b border-gray-200 p-4 dark:border-[#2E2E2A]"
                    >
                        <p class="text-sm text-gray-500 dark:text-[#A1A09A]">
                            {{ allCategories.length }} kategori terdaftar
                        </p>
                        <button
                            class="h-9 rounded-lg bg-emerald-600 px-4 text-sm font-medium text-white"
                        >
                            + Tambah Kategori
                        </button>
                    </div>
                    <div
                        class="grid gap-px bg-gray-200 sm:grid-cols-2 lg:grid-cols-4 dark:bg-[#2E2E2A]"
                    >
                        <div
                            v-for="cat in allCategories"
                            :key="cat.name"
                            class="flex items-center gap-3 bg-white p-4 transition hover:bg-gray-50 dark:bg-[#161615] dark:hover:bg-[#1C1C1A]"
                        >
                            <span
                                class="flex h-10 w-10 items-center justify-center rounded-lg"
                                :style="{ backgroundColor: cat.color + '20' }"
                            >
                                <span
                                    class="h-3 w-3 rounded-full"
                                    :style="{ backgroundColor: cat.color }"
                                />
                            </span>
                            <div>
                                <p
                                    class="text-sm font-medium text-gray-900 dark:text-[#EDEDEC]"
                                >
                                    {{ cat.name }}
                                </p>
                                <p
                                    class="text-xs text-gray-500 dark:text-[#A1A09A]"
                                >
                                    {{ cat.transactions }} transaksi
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Preview -->
            <div class="mb-12">
                <div class="mb-4 flex items-center gap-2">
                    <User class="h-5 w-5 text-emerald-600" />
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-[#EDEDEC]"
                    >
                        Pengaturan
                    </h3>
                </div>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div
                        v-for="setting in settingsMenu"
                        :key="setting.title"
                        class="rounded-xl border border-gray-200 bg-white p-5 transition hover:border-emerald-300 hover:shadow dark:border-[#2E2E2A] dark:bg-[#161615] dark:hover:border-emerald-700"
                    >
                        <div
                            class="mb-3 flex h-9 w-9 items-center justify-center rounded-lg bg-gray-100 dark:bg-[#2E2E2A]"
                        >
                            <component
                                :is="setting.icon"
                                class="h-4 w-4 text-gray-600 dark:text-[#A1A09A]"
                            />
                        </div>
                        <h4
                            class="mb-1 text-sm font-semibold text-gray-900 dark:text-[#EDEDEC]"
                        >
                            {{ setting.title }}
                        </h4>
                        <p
                            class="text-xs leading-relaxed text-gray-500 dark:text-[#A1A09A]"
                        >
                            {{ setting.desc }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Tech Stack -->
        <section
            class="border-t border-gray-200 bg-gray-50 dark:border-[#2E2E2A] dark:bg-[#0a0a0a]"
        >
            <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <h3
                    class="mb-6 text-center text-sm font-semibold tracking-wider text-gray-400 uppercase dark:text-[#A1A09A]"
                >
                    Dibangun dengan teknologi modern
                </h3>
                <div
                    class="flex flex-wrap items-center justify-center gap-6 text-sm font-medium text-gray-500 sm:gap-10 dark:text-[#A1A09A]"
                >
                    <span class="flex items-center gap-2">
                        <span
                            class="flex h-7 w-7 items-center justify-center rounded bg-red-50 text-xs font-bold text-red-500 dark:bg-red-900/20"
                            >L</span
                        >
                        Laravel 12
                    </span>
                    <span class="flex items-center gap-2">
                        <span
                            class="flex h-7 w-7 items-center justify-center rounded bg-green-50 text-xs font-bold text-green-600 dark:bg-green-900/20"
                            >V</span
                        >
                        Vue 3
                    </span>
                    <span class="flex items-center gap-2">
                        <span
                            class="flex h-7 w-7 items-center justify-center rounded bg-blue-50 text-xs font-bold text-blue-500 dark:bg-blue-900/20"
                            >TS</span
                        >
                        TypeScript
                    </span>
                    <span class="flex items-center gap-2">
                        <span
                            class="flex h-7 w-7 items-center justify-center rounded bg-cyan-50 text-xs font-bold text-cyan-600 dark:bg-cyan-900/20"
                            >Tw</span
                        >
                        Tailwind v4
                    </span>
                    <span class="flex items-center gap-2">
                        <span
                            class="flex h-7 w-7 items-center justify-center rounded bg-purple-50 text-xs font-bold text-purple-500 dark:bg-purple-900/20"
                            >I</span
                        >
                        Inertia.js
                    </span>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section
            class="mx-auto max-w-7xl px-4 py-20 text-center sm:px-6 lg:px-8"
        >
            <h2
                class="text-2xl font-bold text-gray-900 sm:text-3xl dark:text-[#EDEDEC]"
            >
                Siap mengelola keuangan Anda?
            </h2>
            <p class="mx-auto mt-3 max-w-xl text-gray-500 dark:text-[#A1A09A]">
                Mulai catat pemasukan dan pengeluaran Anda sekarang. Gratis dan
                mudah digunakan.
            </p>
            <div
                class="mt-8 flex flex-col items-center justify-center gap-4 sm:flex-row"
            >
                <Link
                    v-if="!$page.props.auth.user"
                    :href="register()"
                    class="w-full rounded-lg bg-emerald-600 px-8 py-3 text-base font-semibold text-white shadow-lg shadow-emerald-500/25 transition hover:bg-emerald-700 sm:w-auto"
                >
                    Daftar Sekarang
                </Link>
                <Link
                    v-if="!$page.props.auth.user"
                    :href="login()"
                    class="w-full rounded-lg border border-gray-300 px-8 py-3 text-base font-semibold text-gray-700 transition hover:bg-gray-50 sm:w-auto dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:bg-[#1C1C1A]"
                >
                    Sudah punya akun? Masuk
                </Link>
                <Link
                    v-else
                    :href="dashboard()"
                    class="w-full rounded-lg bg-emerald-600 px-8 py-3 text-base font-semibold text-white shadow-lg shadow-emerald-500/25 transition hover:bg-emerald-700 sm:w-auto"
                >
                    Buka Dashboard
                </Link>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-gray-200 dark:border-[#2E2E2A]">
            <div
                class="mx-auto max-w-7xl px-4 py-6 text-center text-xs text-gray-400 sm:px-6 lg:px-8 dark:text-[#A1A09A]"
            >
                &copy; 2026 Personal Financial Records. Built with Laravel, Vue
                & Inertia.js |
                <a
                    href="https://github.com/sukmaajidigital"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="hover:text-emerald-600 dark:hover:text-emerald-400"
                >
                    sukmaajidigital
                </a>
                |
                <a
                    href="https://www.sukmaaji.my.id"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="hover:text-emerald-600 dark:hover:text-emerald-400"
                >
                    www.sukmaaji.my.id
                </a>
            </div>
        </footer>
    </div>
</template>
