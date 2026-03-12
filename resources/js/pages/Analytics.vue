<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import {
    ArrowDownCircle,
    ArrowUpCircle,
    BarChart3,
    CalendarDays,
    DollarSign,
    Filter,
    RotateCcw,
    TrendingDown,
    TrendingUp,
    Wallet,
    Zap,
} from 'lucide-vue-next';
import { computed, reactive, ref } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import type {
    AnalyticsFilters,
    AnalyticsSummary,
    BreadcrumbItem,
    Category,
    CategoryBreakdown,
    DailyTrend,
    MonthlyTrend,
    Transaction,
} from '@/types';

type Props = {
    summary: AnalyticsSummary;
    dailyTrend: DailyTrend[];
    monthlyTrend: MonthlyTrend[];
    expenseByCategory: CategoryBreakdown[];
    incomeByCategory: CategoryBreakdown[];
    topExpenses: Transaction[];
    topIncomes: Transaction[];
    categories: Pick<Category, 'id' | 'name' | 'color'>[];
    filters: AnalyticsFilters;
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Analytics', href: '/analytics' },
];

// ── Filter State ────────────────────────────────────────────────
const filters = reactive({
    date_from: props.filters.date_from || '',
    date_to: props.filters.date_to || '',
    month: props.filters.month ? String(props.filters.month) : '',
    year: props.filters.year
        ? String(props.filters.year)
        : String(new Date().getFullYear()),
    category_id: props.filters.category_id
        ? String(props.filters.category_id)
        : '',
    type: props.filters.type || '',
});

const filterMode = ref<'year' | 'range'>(
    props.filters.date_from ? 'range' : 'year',
);

function applyFilters() {
    const params: Record<string, string> = {};
    if (filterMode.value === 'range') {
        if (filters.date_from) params.date_from = filters.date_from;
        if (filters.date_to) params.date_to = filters.date_to;
    } else {
        if (filters.year) params.year = filters.year;
        if (filters.month) params.month = filters.month;
    }
    if (filters.category_id) params.category_id = filters.category_id;
    if (filters.type) params.type = filters.type;
    router.get('/analytics', params, {
        preserveState: true,
        preserveScroll: true,
    });
}

function resetFilters() {
    filters.date_from = '';
    filters.date_to = '';
    filters.month = '';
    filters.year = String(new Date().getFullYear());
    filters.category_id = '';
    filters.type = '';
    filterMode.value = 'year';
    router.get('/analytics', {}, { preserveState: false });
}

// ── Helpers ─────────────────────────────────────────────────────
function formatCurrency(amount: number): string {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(amount);
}

function formatDate(date: string): string {
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    }).format(new Date(date));
}

function formatMonth(month: string): string {
    const [year, m] = month.split('-');
    const date = new Date(Number(year), Number(m) - 1);
    return new Intl.DateTimeFormat('id-ID', {
        month: 'short',
        year: '2-digit',
    }).format(date);
}

function formatShortDate(date: string): string {
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'short',
    }).format(new Date(date));
}

const months = [
    { value: '1', label: 'Januari' },
    { value: '2', label: 'Februari' },
    { value: '3', label: 'Maret' },
    { value: '4', label: 'April' },
    { value: '5', label: 'Mei' },
    { value: '6', label: 'Juni' },
    { value: '7', label: 'Juli' },
    { value: '8', label: 'Agustus' },
    { value: '9', label: 'September' },
    { value: '10', label: 'Oktober' },
    { value: '11', label: 'November' },
    { value: '12', label: 'Desember' },
];

const currentYear = new Date().getFullYear();
const years = Array.from({ length: 5 }, (_, i) => String(currentYear - i));

// ── SVG Line Chart Computations ─────────────────────────────────
const chartWidth = 800;
const chartHeight = 300;
const chartPadding = { top: 20, right: 20, bottom: 40, left: 20 };
const plotWidth = chartWidth - chartPadding.left - chartPadding.right;
const plotHeight = chartHeight - chartPadding.top - chartPadding.bottom;

const maxDailyValue = computed(() => {
    if (props.dailyTrend.length === 0) return 1;
    return Math.max(
        ...props.dailyTrend.flatMap((t) => [
            Number(t.income),
            Number(t.expense),
        ]),
        1,
    );
});

function getX(index: number, total: number): number {
    if (total <= 1) return chartPadding.left + plotWidth / 2;
    return chartPadding.left + (index / (total - 1)) * plotWidth;
}

function getY(value: number): number {
    return (
        chartPadding.top +
        plotHeight -
        (value / maxDailyValue.value) * plotHeight
    );
}

function buildSmoothPath(data: { value: number }[]): string {
    if (data.length === 0) return '';
    if (data.length === 1) {
        const x = getX(0, 1);
        const y = getY(data[0].value);
        return `M ${x} ${y}`;
    }

    const points = data.map((d, i) => ({
        x: getX(i, data.length),
        y: getY(d.value),
    }));

    let path = `M ${points[0].x} ${points[0].y}`;

    for (let i = 0; i < points.length - 1; i++) {
        const p0 = points[Math.max(0, i - 1)];
        const p1 = points[i];
        const p2 = points[i + 1];
        const p3 = points[Math.min(points.length - 1, i + 2)];

        const tension = 0.3;
        const cp1x = p1.x + (p2.x - p0.x) * tension;
        const cp1y = p1.y + (p2.y - p0.y) * tension;
        const cp2x = p2.x - (p3.x - p1.x) * tension;
        const cp2y = p2.y - (p3.y - p1.y) * tension;

        path += ` C ${cp1x} ${cp1y}, ${cp2x} ${cp2y}, ${p2.x} ${p2.y}`;
    }

    return path;
}

function buildAreaPath(data: { value: number }[]): string {
    const linePath = buildSmoothPath(data);
    if (!linePath) return '';
    const lastX = getX(data.length - 1, data.length);
    const firstX = getX(0, data.length);
    const bottom = chartPadding.top + plotHeight;
    return `${linePath} L ${lastX} ${bottom} L ${firstX} ${bottom} Z`;
}

const incomeLineData = computed(() =>
    props.dailyTrend.map((t) => ({ value: Number(t.income) })),
);

const expenseLineData = computed(() =>
    props.dailyTrend.map((t) => ({ value: Number(t.expense) })),
);

const incomePath = computed(() => buildSmoothPath(incomeLineData.value));
const expensePath = computed(() => buildSmoothPath(expenseLineData.value));
const incomeAreaPath = computed(() => buildAreaPath(incomeLineData.value));
const expenseAreaPath = computed(() => buildAreaPath(expenseLineData.value));

const yAxisTicks = computed(() => {
    const max = maxDailyValue.value;
    const ticks = [];
    const step = max / 4;
    for (let i = 0; i <= 4; i++) {
        ticks.push(Math.round(step * i));
    }
    return ticks;
});

const xAxisLabels = computed(() => {
    const data = props.dailyTrend;
    if (data.length <= 10)
        return data.map((d, i) => ({
            index: i,
            label: formatShortDate(d.day),
        }));
    const step = Math.max(1, Math.floor(data.length / 8));
    return data
        .map((d, i) => ({ index: i, label: formatShortDate(d.day) }))
        .filter((_, i) => i % step === 0 || i === data.length - 1);
});

// ── Donut chart helpers ─────────────────────────────────────────
function getDonutSegments(data: CategoryBreakdown[]) {
    const total = data.reduce((sum, c) => sum + Number(c.total), 0);
    if (total === 0) return [];
    const radius = 80;
    const cx = 100;
    const cy = 100;
    let currentAngle = -90; // Start from top

    return data.map((item) => {
        const percentage = Number(item.total) / total;
        const angle = percentage * 360;
        const startAngle = currentAngle;
        const endAngle = currentAngle + angle;
        currentAngle = endAngle;

        const startRad = (startAngle * Math.PI) / 180;
        const endRad = (endAngle * Math.PI) / 180;

        const x1 = cx + radius * Math.cos(startRad);
        const y1 = cy + radius * Math.sin(startRad);
        const x2 = cx + radius * Math.cos(endRad);
        const y2 = cy + radius * Math.sin(endRad);
        const largeArc = angle > 180 ? 1 : 0;

        return {
            path: `M ${cx} ${cy} L ${x1} ${y1} A ${radius} ${radius} 0 ${largeArc} 1 ${x2} ${y2} Z`,
            color: item.color,
            name: item.name,
            total: Number(item.total),
            percentage: (percentage * 100).toFixed(1),
            count: item.count,
        };
    });
}

const expenseDonutSegments = computed(() =>
    getDonutSegments(props.expenseByCategory),
);
const incomeDonutSegments = computed(() =>
    getDonutSegments(props.incomeByCategory),
);

const totalExpense = computed(() =>
    props.expenseByCategory.reduce((s, c) => s + Number(c.total), 0),
);
const totalIncome = computed(() =>
    props.incomeByCategory.reduce((s, c) => s + Number(c.total), 0),
);

// ── Monthly Trend ──────────────────────────────────────────────
const maxMonthlyValue = computed(() => {
    return Math.max(
        ...props.monthlyTrend.flatMap((t) => [
            Number(t.income),
            Number(t.expense),
        ]),
        1,
    );
});

// ── Tooltip ────────────────────────────────────────────────────
const tooltipData = ref<{
    show: boolean;
    x: number;
    y: number;
    day: string;
    income: number;
    expense: number;
}>({ show: false, x: 0, y: 0, day: '', income: 0, expense: 0 });

function showTooltip(event: MouseEvent, index: number) {
    const trend = props.dailyTrend[index];
    if (!trend) return;
    const svg = (event.target as SVGElement).closest('svg');
    if (!svg) return;
    const rect = svg.getBoundingClientRect();
    tooltipData.value = {
        show: true,
        x: event.clientX - rect.left,
        y: event.clientY - rect.top - 10,
        day: trend.day,
        income: Number(trend.income),
        expense: Number(trend.expense),
    };
}

function hideTooltip() {
    tooltipData.value.show = false;
}

// ── Active filter description ───────────────────────────────────
const filterDescription = computed(() => {
    const parts: string[] = [];
    if (
        filterMode.value === 'range' &&
        props.filters.date_from &&
        props.filters.date_to
    ) {
        parts.push(
            `${formatDate(props.filters.date_from)} – ${formatDate(props.filters.date_to)}`,
        );
    } else {
        if (props.filters.month) {
            const m = months.find(
                (m) => m.value === String(props.filters.month),
            );
            parts.push(m?.label || '');
        }
        parts.push(String(props.filters.year || currentYear));
    }
    if (props.filters.type) {
        parts.push(
            props.filters.type === 'income' ? 'Pemasukan' : 'Pengeluaran',
        );
    }
    if (props.filters.category_id) {
        const cat = props.categories.find(
            (c) => c.id === Number(props.filters.category_id),
        );
        if (cat) parts.push(cat.name);
    }
    return parts.join(' · ');
});
</script>

<template>
    <Head title="Analytics" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4 md:p-6">
            <!-- Page Header -->
            <div
                class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Analytics</h1>
                    <p class="text-sm text-muted-foreground">
                        {{ filterDescription }}
                    </p>
                </div>
            </div>

            <!-- Filter Bar -->
            <Card>
                <CardContent class="pt-6">
                    <div class="flex flex-col gap-4">
                        <!-- Filter Mode Toggle -->
                        <div class="flex items-center gap-2">
                            <Button
                                size="sm"
                                :variant="
                                    filterMode === 'year'
                                        ? 'default'
                                        : 'outline'
                                "
                                @click="filterMode = 'year'"
                            >
                                <CalendarDays class="mr-1.5 size-3.5" />
                                Bulan/Tahun
                            </Button>
                            <Button
                                size="sm"
                                :variant="
                                    filterMode === 'range'
                                        ? 'default'
                                        : 'outline'
                                "
                                @click="filterMode = 'range'"
                            >
                                <Filter class="mr-1.5 size-3.5" />
                                Rentang Tanggal
                            </Button>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                            <!-- Year/Month OR Date Range -->
                            <template v-if="filterMode === 'year'">
                                <div>
                                    <label
                                        class="mb-1.5 block text-xs font-medium text-muted-foreground"
                                        >Tahun</label
                                    >
                                    <Select v-model="filters.year">
                                        <SelectTrigger
                                            ><SelectValue
                                                placeholder="Pilih tahun"
                                        /></SelectTrigger>
                                        <SelectContent>
                                            <SelectItem
                                                v-for="y in years"
                                                :key="y"
                                                :value="y"
                                                >{{ y }}</SelectItem
                                            >
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div>
                                    <label
                                        class="mb-1.5 block text-xs font-medium text-muted-foreground"
                                        >Bulan</label
                                    >
                                    <Select v-model="filters.month">
                                        <SelectTrigger
                                            ><SelectValue
                                                placeholder="Semua bulan"
                                        /></SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value=""
                                                >Semua Bulan</SelectItem
                                            >
                                            <SelectItem
                                                v-for="m in months"
                                                :key="m.value"
                                                :value="m.value"
                                                >{{ m.label }}</SelectItem
                                            >
                                        </SelectContent>
                                    </Select>
                                </div>
                            </template>
                            <template v-else>
                                <div>
                                    <label
                                        class="mb-1.5 block text-xs font-medium text-muted-foreground"
                                        >Dari Tanggal</label
                                    >
                                    <input
                                        v-model="filters.date_from"
                                        type="date"
                                        class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-ring focus-visible:outline-none"
                                    />
                                </div>
                                <div>
                                    <label
                                        class="mb-1.5 block text-xs font-medium text-muted-foreground"
                                        >Sampai Tanggal</label
                                    >
                                    <input
                                        v-model="filters.date_to"
                                        type="date"
                                        class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-ring focus-visible:outline-none"
                                    />
                                </div>
                            </template>

                            <!-- Category Filter -->
                            <div>
                                <label
                                    class="mb-1.5 block text-xs font-medium text-muted-foreground"
                                    >Kategori</label
                                >
                                <Select v-model="filters.category_id">
                                    <SelectTrigger
                                        ><SelectValue
                                            placeholder="Semua kategori"
                                    /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value=""
                                            >Semua Kategori</SelectItem
                                        >
                                        <SelectItem
                                            v-for="cat in props.categories"
                                            :key="cat.id"
                                            :value="String(cat.id)"
                                        >
                                            <div
                                                class="flex items-center gap-2"
                                            >
                                                <span
                                                    class="size-2 rounded-full"
                                                    :style="{
                                                        backgroundColor:
                                                            cat.color,
                                                    }"
                                                />
                                                {{ cat.name }}
                                            </div>
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>

                            <!-- Type Filter -->
                            <div>
                                <label
                                    class="mb-1.5 block text-xs font-medium text-muted-foreground"
                                    >Tipe</label
                                >
                                <Select v-model="filters.type">
                                    <SelectTrigger
                                        ><SelectValue placeholder="Semua tipe"
                                    /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value=""
                                            >Semua Tipe</SelectItem
                                        >
                                        <SelectItem value="income"
                                            >Pemasukan</SelectItem
                                        >
                                        <SelectItem value="expense"
                                            >Pengeluaran</SelectItem
                                        >
                                    </SelectContent>
                                </Select>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-end gap-2">
                                <Button @click="applyFilters" class="flex-1">
                                    <Filter class="mr-1.5 size-3.5" />
                                    Terapkan
                                </Button>
                                <Button variant="outline" @click="resetFilters">
                                    <RotateCcw class="size-3.5" />
                                </Button>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Summary Cards -->
            <div
                class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6"
            >
                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between pb-2"
                    >
                        <CardTitle class="text-sm font-medium"
                            >Total Pemasukan</CardTitle
                        >
                        <TrendingUp class="size-4 text-green-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-xl font-bold text-green-600">
                            {{ formatCurrency(props.summary.totalIncome) }}
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between pb-2"
                    >
                        <CardTitle class="text-sm font-medium"
                            >Total Pengeluaran</CardTitle
                        >
                        <TrendingDown class="size-4 text-red-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-xl font-bold text-red-600">
                            {{ formatCurrency(props.summary.totalExpense) }}
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between pb-2"
                    >
                        <CardTitle class="text-sm font-medium"
                            >Saldo Bersih</CardTitle
                        >
                        <Wallet class="size-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div
                            class="text-xl font-bold"
                            :class="
                                props.summary.balance >= 0
                                    ? 'text-green-600'
                                    : 'text-red-600'
                            "
                        >
                            {{ formatCurrency(props.summary.balance) }}
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between pb-2"
                    >
                        <CardTitle class="text-sm font-medium"
                            >Total Transaksi</CardTitle
                        >
                        <BarChart3 class="size-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-xl font-bold">
                            {{ props.summary.totalTransactions }}
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between pb-2"
                    >
                        <CardTitle class="text-sm font-medium"
                            >Rata-rata Pengeluaran</CardTitle
                        >
                        <DollarSign class="size-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-xl font-bold">
                            {{ formatCurrency(props.summary.avgExpense) }}
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between pb-2"
                    >
                        <CardTitle class="text-sm font-medium"
                            >Rasio Tabungan</CardTitle
                        >
                        <Zap class="size-4 text-amber-500" />
                    </CardHeader>
                    <CardContent>
                        <div
                            class="text-xl font-bold"
                            :class="
                                props.summary.totalIncome > 0
                                    ? props.summary.balance /
                                          props.summary.totalIncome >=
                                      0.2
                                        ? 'text-green-600'
                                        : 'text-amber-500'
                                    : 'text-muted-foreground'
                            "
                        >
                            {{
                                props.summary.totalIncome > 0
                                    ? (
                                          (props.summary.balance /
                                              props.summary.totalIncome) *
                                          100
                                      ).toFixed(1)
                                    : '0'
                            }}%
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- SVG Line Chart: Income vs Expense Trend -->
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div class="space-y-1">
                            <CardTitle>Tren Harian</CardTitle>
                            <CardDescription
                                >Pemasukan vs Pengeluaran per
                                hari</CardDescription
                            >
                        </div>
                        <div class="flex items-center gap-4 text-sm">
                            <div class="flex items-center gap-1.5">
                                <span
                                    class="size-3 rounded-full bg-green-500"
                                />
                                <span class="text-muted-foreground"
                                    >Pemasukan</span
                                >
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="size-3 rounded-full bg-red-500" />
                                <span class="text-muted-foreground"
                                    >Pengeluaran</span
                                >
                            </div>
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <div
                        v-if="props.dailyTrend.length === 0"
                        class="flex h-64 items-center justify-center text-sm text-muted-foreground"
                    >
                        Tidak ada data untuk periode ini.
                    </div>
                    <div v-else class="relative overflow-x-auto">
                        <svg
                            :viewBox="`0 0 ${chartWidth} ${chartHeight}`"
                            class="w-full min-w-[600px]"
                            preserveAspectRatio="xMidYMid meet"
                        >
                            <defs>
                                <linearGradient
                                    id="incomeGradient"
                                    x1="0"
                                    y1="0"
                                    x2="0"
                                    y2="1"
                                >
                                    <stop
                                        offset="0%"
                                        stop-color="rgb(34, 197, 94)"
                                        stop-opacity="0.3"
                                    />
                                    <stop
                                        offset="100%"
                                        stop-color="rgb(34, 197, 94)"
                                        stop-opacity="0.02"
                                    />
                                </linearGradient>
                                <linearGradient
                                    id="expenseGradient"
                                    x1="0"
                                    y1="0"
                                    x2="0"
                                    y2="1"
                                >
                                    <stop
                                        offset="0%"
                                        stop-color="rgb(239, 68, 68)"
                                        stop-opacity="0.3"
                                    />
                                    <stop
                                        offset="100%"
                                        stop-color="rgb(239, 68, 68)"
                                        stop-opacity="0.02"
                                    />
                                </linearGradient>
                            </defs>

                            <!-- Grid lines -->
                            <line
                                v-for="tick in yAxisTicks"
                                :key="'grid-' + tick"
                                :x1="chartPadding.left"
                                :y1="getY(tick)"
                                :x2="chartWidth - chartPadding.right"
                                :y2="getY(tick)"
                                stroke="currentColor"
                                class="text-border"
                                stroke-opacity="0.3"
                                stroke-dasharray="4 4"
                            />

                            <!-- Y-axis labels -->
                            <text
                                v-for="tick in yAxisTicks"
                                :key="'label-' + tick"
                                :x="chartPadding.left - 4"
                                :y="getY(tick) + 4"
                                text-anchor="end"
                                class="fill-muted-foreground text-[10px]"
                            >
                                {{
                                    tick >= 1000000
                                        ? (tick / 1000000).toFixed(1) + 'M'
                                        : tick >= 1000
                                          ? (tick / 1000).toFixed(0) + 'K'
                                          : tick
                                }}
                            </text>

                            <!-- X-axis labels -->
                            <text
                                v-for="label in xAxisLabels"
                                :key="'x-' + label.index"
                                :x="getX(label.index, props.dailyTrend.length)"
                                :y="chartHeight - 8"
                                text-anchor="middle"
                                class="fill-muted-foreground text-[10px]"
                            >
                                {{ label.label }}
                            </text>

                            <!-- Area fills -->
                            <path
                                :d="incomeAreaPath"
                                fill="url(#incomeGradient)"
                            />
                            <path
                                :d="expenseAreaPath"
                                fill="url(#expenseGradient)"
                            />

                            <!-- Lines -->
                            <path
                                :d="incomePath"
                                fill="none"
                                stroke="rgb(34, 197, 94)"
                                stroke-width="2.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path
                                :d="expensePath"
                                fill="none"
                                stroke="rgb(239, 68, 68)"
                                stroke-width="2.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />

                            <!-- Dots & hover zones -->
                            <g
                                v-for="(trend, i) in props.dailyTrend"
                                :key="'dot-' + i"
                            >
                                <circle
                                    :cx="getX(i, props.dailyTrend.length)"
                                    :cy="getY(Number(trend.income))"
                                    r="3"
                                    fill="rgb(34, 197, 94)"
                                    class="opacity-0 transition-opacity hover:opacity-100"
                                />
                                <circle
                                    :cx="getX(i, props.dailyTrend.length)"
                                    :cy="getY(Number(trend.expense))"
                                    r="3"
                                    fill="rgb(239, 68, 68)"
                                    class="opacity-0 transition-opacity hover:opacity-100"
                                />
                                <!-- Invisible hit area for tooltip -->
                                <rect
                                    :x="getX(i, props.dailyTrend.length) - 10"
                                    :y="chartPadding.top"
                                    width="20"
                                    :height="plotHeight"
                                    fill="transparent"
                                    @mouseover="showTooltip($event, i)"
                                    @mouseleave="hideTooltip"
                                />
                            </g>
                        </svg>

                        <!-- Tooltip -->
                        <div
                            v-if="tooltipData.show"
                            class="pointer-events-none absolute z-10 rounded-lg border bg-popover px-3 py-2 text-xs shadow-lg"
                            :style="{
                                left: tooltipData.x + 'px',
                                top: tooltipData.y + 'px',
                                transform: 'translate(-50%, -100%)',
                            }"
                        >
                            <p class="mb-1 font-medium">
                                {{ formatShortDate(tooltipData.day) }}
                            </p>
                            <p class="text-green-600">
                                + {{ formatCurrency(tooltipData.income) }}
                            </p>
                            <p class="text-red-600">
                                - {{ formatCurrency(tooltipData.expense) }}
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Category Breakdown Row (Donut Charts) -->
            <div class="grid gap-4 lg:grid-cols-2">
                <!-- Expense Donut -->
                <Card>
                    <CardHeader>
                        <CardTitle>Pengeluaran per Kategori</CardTitle>
                        <CardDescription
                            >Total:
                            {{ formatCurrency(totalExpense) }}</CardDescription
                        >
                    </CardHeader>
                    <CardContent>
                        <div
                            v-if="props.expenseByCategory.length === 0"
                            class="flex h-48 items-center justify-center text-sm text-muted-foreground"
                        >
                            Tidak ada data pengeluaran.
                        </div>
                        <div
                            v-else
                            class="flex flex-col items-center gap-6 sm:flex-row"
                        >
                            <!-- Donut -->
                            <svg viewBox="0 0 200 200" class="size-44 shrink-0">
                                <path
                                    v-for="(seg, i) in expenseDonutSegments"
                                    :key="'exp-' + i"
                                    :d="seg.path"
                                    :fill="seg.color"
                                    stroke="var(--color-background)"
                                    stroke-width="2"
                                    class="transition-opacity hover:opacity-80"
                                />
                                <circle
                                    cx="100"
                                    cy="100"
                                    r="50"
                                    fill="var(--color-background)"
                                />
                                <text
                                    x="100"
                                    y="96"
                                    text-anchor="middle"
                                    class="fill-foreground text-lg font-bold"
                                    dominant-baseline="middle"
                                >
                                    {{ expenseDonutSegments.length }}
                                </text>
                                <text
                                    x="100"
                                    y="114"
                                    text-anchor="middle"
                                    class="fill-muted-foreground text-[10px]"
                                    dominant-baseline="middle"
                                >
                                    kategori
                                </text>
                            </svg>
                            <!-- Legend -->
                            <div class="flex-1 space-y-2">
                                <div
                                    v-for="seg in expenseDonutSegments"
                                    :key="seg.name"
                                    class="flex items-center justify-between text-sm"
                                >
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="size-2.5 rounded-full"
                                            :style="{
                                                backgroundColor: seg.color,
                                            }"
                                        />
                                        <span>{{ seg.name }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium">{{
                                            formatCurrency(seg.total)
                                        }}</span>
                                        <Badge
                                            variant="outline"
                                            class="text-[10px]"
                                            >{{ seg.percentage }}%</Badge
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Income Donut -->
                <Card>
                    <CardHeader>
                        <CardTitle>Pemasukan per Kategori</CardTitle>
                        <CardDescription
                            >Total:
                            {{ formatCurrency(totalIncome) }}</CardDescription
                        >
                    </CardHeader>
                    <CardContent>
                        <div
                            v-if="props.incomeByCategory.length === 0"
                            class="flex h-48 items-center justify-center text-sm text-muted-foreground"
                        >
                            Tidak ada data pemasukan.
                        </div>
                        <div
                            v-else
                            class="flex flex-col items-center gap-6 sm:flex-row"
                        >
                            <!-- Donut -->
                            <svg viewBox="0 0 200 200" class="size-44 shrink-0">
                                <path
                                    v-for="(seg, i) in incomeDonutSegments"
                                    :key="'inc-' + i"
                                    :d="seg.path"
                                    :fill="seg.color"
                                    stroke="var(--color-background)"
                                    stroke-width="2"
                                    class="transition-opacity hover:opacity-80"
                                />
                                <circle
                                    cx="100"
                                    cy="100"
                                    r="50"
                                    fill="var(--color-background)"
                                />
                                <text
                                    x="100"
                                    y="96"
                                    text-anchor="middle"
                                    class="fill-foreground text-lg font-bold"
                                    dominant-baseline="middle"
                                >
                                    {{ incomeDonutSegments.length }}
                                </text>
                                <text
                                    x="100"
                                    y="114"
                                    text-anchor="middle"
                                    class="fill-muted-foreground text-[10px]"
                                    dominant-baseline="middle"
                                >
                                    kategori
                                </text>
                            </svg>
                            <!-- Legend -->
                            <div class="flex-1 space-y-2">
                                <div
                                    v-for="seg in incomeDonutSegments"
                                    :key="seg.name"
                                    class="flex items-center justify-between text-sm"
                                >
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="size-2.5 rounded-full"
                                            :style="{
                                                backgroundColor: seg.color,
                                            }"
                                        />
                                        <span>{{ seg.name }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium">{{
                                            formatCurrency(seg.total)
                                        }}</span>
                                        <Badge
                                            variant="outline"
                                            class="text-[10px]"
                                            >{{ seg.percentage }}%</Badge
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Monthly Trend Bar Chart -->
            <Card>
                <CardHeader>
                    <CardTitle>Tren Bulanan</CardTitle>
                    <CardDescription
                        >Perbandingan pemasukan dan pengeluaran per
                        bulan</CardDescription
                    >
                </CardHeader>
                <CardContent>
                    <div
                        v-if="props.monthlyTrend.length === 0"
                        class="flex h-48 items-center justify-center text-sm text-muted-foreground"
                    >
                        Tidak ada data.
                    </div>
                    <div v-else class="space-y-3">
                        <div
                            v-for="trend in props.monthlyTrend"
                            :key="trend.month"
                            class="space-y-1.5"
                        >
                            <div
                                class="flex items-center justify-between text-sm"
                            >
                                <span class="w-16 font-medium">{{
                                    formatMonth(trend.month)
                                }}</span>
                                <div class="flex gap-4 text-xs">
                                    <span class="text-green-600"
                                        >+{{
                                            formatCurrency(Number(trend.income))
                                        }}</span
                                    >
                                    <span class="text-red-600"
                                        >-{{
                                            formatCurrency(
                                                Number(trend.expense),
                                            )
                                        }}</span
                                    >
                                    <span
                                        class="font-medium"
                                        :class="
                                            Number(trend.income) -
                                                Number(trend.expense) >=
                                            0
                                                ? 'text-green-600'
                                                : 'text-red-600'
                                        "
                                    >
                                        {{
                                            formatCurrency(
                                                Number(trend.income) -
                                                    Number(trend.expense),
                                            )
                                        }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex gap-0.5">
                                <div
                                    class="h-4 rounded-l-sm bg-green-500/80 transition-all"
                                    :style="{
                                        width: `${(Number(trend.income) / maxMonthlyValue) * 100}%`,
                                        minWidth:
                                            Number(trend.income) > 0
                                                ? '4px'
                                                : '0',
                                    }"
                                />
                                <div
                                    class="h-4 rounded-r-sm bg-red-500/80 transition-all"
                                    :style="{
                                        width: `${(Number(trend.expense) / maxMonthlyValue) * 100}%`,
                                        minWidth:
                                            Number(trend.expense) > 0
                                                ? '4px'
                                                : '0',
                                    }"
                                />
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Top Transactions -->
            <div class="grid gap-4 lg:grid-cols-2">
                <!-- Top Income -->
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2">
                            <ArrowUpCircle class="size-5 text-green-600" />
                            <div>
                                <CardTitle>Top 5 Pemasukan Tertinggi</CardTitle>
                                <CardDescription
                                    >Transaksi pemasukan
                                    terbesar</CardDescription
                                >
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <div
                            v-if="props.topIncomes.length === 0"
                            class="flex h-32 items-center justify-center text-sm text-muted-foreground"
                        >
                            Tidak ada data pemasukan.
                        </div>
                        <div
                            v-for="(trx, i) in props.topIncomes"
                            :key="trx.id"
                            class="flex items-center justify-between rounded-lg border p-3 transition-colors hover:bg-muted/50"
                        >
                            <div class="flex items-center gap-3">
                                <span
                                    class="flex size-7 items-center justify-center rounded-full bg-green-100 text-xs font-bold text-green-700 dark:bg-green-950 dark:text-green-400"
                                >
                                    {{ i + 1 }}
                                </span>
                                <div>
                                    <p class="text-sm font-medium">
                                        {{ trx.description }}
                                    </p>
                                    <div
                                        class="flex items-center gap-2 text-xs text-muted-foreground"
                                    >
                                        <span>{{ formatDate(trx.date) }}</span>
                                        <span
                                            v-if="trx.category"
                                            class="flex items-center gap-1"
                                        >
                                            <span
                                                class="size-1.5 rounded-full"
                                                :style="{
                                                    backgroundColor:
                                                        trx.category.color,
                                                }"
                                            />
                                            {{ trx.category.name }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <span class="text-sm font-bold text-green-600"
                                >+{{ formatCurrency(Number(trx.amount)) }}</span
                            >
                        </div>
                    </CardContent>
                </Card>

                <!-- Top Expense -->
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2">
                            <ArrowDownCircle class="size-5 text-red-600" />
                            <div>
                                <CardTitle
                                    >Top 5 Pengeluaran Tertinggi</CardTitle
                                >
                                <CardDescription
                                    >Transaksi pengeluaran
                                    terbesar</CardDescription
                                >
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <div
                            v-if="props.topExpenses.length === 0"
                            class="flex h-32 items-center justify-center text-sm text-muted-foreground"
                        >
                            Tidak ada data pengeluaran.
                        </div>
                        <div
                            v-for="(trx, i) in props.topExpenses"
                            :key="trx.id"
                            class="flex items-center justify-between rounded-lg border p-3 transition-colors hover:bg-muted/50"
                        >
                            <div class="flex items-center gap-3">
                                <span
                                    class="flex size-7 items-center justify-center rounded-full bg-red-100 text-xs font-bold text-red-700 dark:bg-red-950 dark:text-red-400"
                                >
                                    {{ i + 1 }}
                                </span>
                                <div>
                                    <p class="text-sm font-medium">
                                        {{ trx.description }}
                                    </p>
                                    <div
                                        class="flex items-center gap-2 text-xs text-muted-foreground"
                                    >
                                        <span>{{ formatDate(trx.date) }}</span>
                                        <span
                                            v-if="trx.category"
                                            class="flex items-center gap-1"
                                        >
                                            <span
                                                class="size-1.5 rounded-full"
                                                :style="{
                                                    backgroundColor:
                                                        trx.category.color,
                                                }"
                                            />
                                            {{ trx.category.name }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <span class="text-sm font-bold text-red-600"
                                >-{{ formatCurrency(Number(trx.amount)) }}</span
                            >
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
