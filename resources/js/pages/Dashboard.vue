<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowDownCircle,
    ArrowUpCircle,
    DollarSign,
    FolderOpen,
    Plus,
    Receipt,
    TrendingUp,
    Wallet,
} from 'lucide-vue-next';
import { computed } from 'vue';
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
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import type {
    BreadcrumbItem,
    DashboardSummary,
    ExpenseByCategory,
    MonthlyTrend,
    Transaction,
} from '@/types';

type Props = {
    summary: DashboardSummary;
    monthlyTrend: MonthlyTrend[];
    expenseByCategory: ExpenseByCategory[];
    recentTransactions: Transaction[];
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
];

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
    return new Intl.DateTimeFormat('id-ID', { month: 'short' }).format(date);
}

const maxTrendValue = computed(() => {
    return Math.max(
        ...props.monthlyTrend.flatMap((t) => [
            Number(t.income),
            Number(t.expense),
        ]),
        1,
    );
});

const totalExpenseCategory = computed(() => {
    return props.expenseByCategory.reduce((sum, c) => sum + Number(c.total), 0);
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4 md:p-6">
            <!-- Quick Actions (Mobile) -->
            <div class="grid grid-cols-2 gap-3 md:hidden">
                <Button as-child size="lg" class="w-full">
                    <Link href="/transactions/create">
                        <Plus class="mr-2 size-4" />
                        Tambah Transaksi
                    </Link>
                </Button>
                <Button as-child variant="outline" size="lg" class="w-full">
                    <Link href="/categories">
                        <FolderOpen class="mr-2 size-4" />
                        Kategori
                    </Link>
                </Button>
            </div>

            <!-- Summary Cards -->
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between pb-2"
                    >
                        <CardTitle class="text-sm font-medium"
                            >Saldo Total</CardTitle
                        >
                        <Wallet class="size-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div
                            class="text-2xl font-bold"
                            :class="
                                props.summary.balance >= 0
                                    ? 'text-green-600'
                                    : 'text-red-600'
                            "
                        >
                            {{ formatCurrency(props.summary.balance) }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            Seluruh waktu
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between pb-2"
                    >
                        <CardTitle class="text-sm font-medium"
                            >Pemasukan Bulan Ini</CardTitle
                        >
                        <TrendingUp class="size-4 text-green-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-green-600">
                            {{ formatCurrency(props.summary.totalIncome) }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            Bulan berjalan
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between pb-2"
                    >
                        <CardTitle class="text-sm font-medium"
                            >Pengeluaran Bulan Ini</CardTitle
                        >
                        <DollarSign class="size-4 text-red-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-red-600">
                            {{ formatCurrency(props.summary.totalExpense) }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            Bulan berjalan
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between pb-2"
                    >
                        <CardTitle class="text-sm font-medium"
                            >Total Transaksi</CardTitle
                        >
                        <Receipt class="size-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                            {{ props.summary.totalTransactions }}
                        </div>
                        <p class="text-xs text-muted-foreground">Bulan ini</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Charts Row -->
            <div class="grid gap-4 lg:grid-cols-5">
                <!-- Monthly Trend (Bar chart) -->
                <Card class="lg:col-span-3">
                    <CardHeader>
                        <CardTitle>Tren Bulanan</CardTitle>
                        <CardDescription
                            >Pemasukan vs Pengeluaran (6 bulan
                            terakhir)</CardDescription
                        >
                    </CardHeader>
                    <CardContent>
                        <div
                            v-if="props.monthlyTrend.length === 0"
                            class="flex h-48 items-center justify-center text-sm text-muted-foreground"
                        >
                            Belum ada data transaksi.
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
                                    <span class="font-medium">{{
                                        formatMonth(trend.month)
                                    }}</span>
                                    <div class="flex gap-3 text-xs">
                                        <span class="text-green-600"
                                            >+{{
                                                formatCurrency(
                                                    Number(trend.income),
                                                )
                                            }}</span
                                        >
                                        <span class="text-red-600"
                                            >-{{
                                                formatCurrency(
                                                    Number(trend.expense),
                                                )
                                            }}</span
                                        >
                                    </div>
                                </div>
                                <div class="flex gap-1">
                                    <div
                                        class="h-3 rounded-sm bg-green-500 transition-all"
                                        :style="{
                                            width: `${(Number(trend.income) / maxTrendValue) * 100}%`,
                                            minWidth:
                                                Number(trend.income) > 0
                                                    ? '4px'
                                                    : '0',
                                        }"
                                    />
                                    <div
                                        class="h-3 rounded-sm bg-red-500 transition-all"
                                        :style="{
                                            width: `${(Number(trend.expense) / maxTrendValue) * 100}%`,
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

                <!-- Expense by Category -->
                <Card class="lg:col-span-2">
                    <CardHeader>
                        <CardTitle>Pengeluaran per Kategori</CardTitle>
                        <CardDescription>Bulan ini</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div
                            v-if="props.expenseByCategory.length === 0"
                            class="flex h-48 items-center justify-center text-sm text-muted-foreground"
                        >
                            Belum ada pengeluaran bulan ini.
                        </div>
                        <div v-else class="space-y-3">
                            <div
                                v-for="category in props.expenseByCategory"
                                :key="category.name"
                                class="space-y-1"
                            >
                                <div
                                    class="flex items-center justify-between text-sm"
                                >
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="size-3 rounded-full"
                                            :style="{
                                                backgroundColor: category.color,
                                            }"
                                        />
                                        <span>{{ category.name }}</span>
                                    </div>
                                    <span class="font-medium">{{
                                        formatCurrency(Number(category.total))
                                    }}</span>
                                </div>
                                <div
                                    class="h-2 w-full overflow-hidden rounded-full bg-muted"
                                >
                                    <div
                                        class="h-full rounded-full transition-all"
                                        :style="{
                                            width: `${(Number(category.total) / totalExpenseCategory) * 100}%`,
                                            backgroundColor: category.color,
                                        }"
                                    />
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Recent Transactions -->
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <div>
                        <CardTitle>Transaksi Terbaru</CardTitle>
                        <CardDescription
                            >5 transaksi terakhir Anda</CardDescription
                        >
                    </div>
                    <Button variant="outline" size="sm" as-child>
                        <Link href="/transactions">Lihat Semua</Link>
                    </Button>
                </CardHeader>
                <CardContent class="p-0">
                    <!-- Mobile -->
                    <div class="block md:hidden">
                        <div
                            v-if="props.recentTransactions.length === 0"
                            class="p-6 text-center text-sm text-muted-foreground"
                        >
                            Belum ada transaksi.
                        </div>
                        <div
                            v-for="transaction in props.recentTransactions"
                            :key="transaction.id"
                            class="flex items-center justify-between border-b px-4 py-3 last:border-b-0"
                        >
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex size-8 items-center justify-center rounded-full"
                                    :class="
                                        transaction.type === 'income'
                                            ? 'bg-green-100 dark:bg-green-950'
                                            : 'bg-red-100 dark:bg-red-950'
                                    "
                                >
                                    <ArrowUpCircle
                                        v-if="transaction.type === 'income'"
                                        class="size-4 text-green-600"
                                    />
                                    <ArrowDownCircle
                                        v-else
                                        class="size-4 text-red-600"
                                    />
                                </div>
                                <div>
                                    <p class="text-sm font-medium">
                                        {{ transaction.description }}
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ formatDate(transaction.date) }}
                                    </p>
                                </div>
                            </div>
                            <span
                                class="text-sm font-semibold"
                                :class="
                                    transaction.type === 'income'
                                        ? 'text-green-600'
                                        : 'text-red-600'
                                "
                            >
                                {{ transaction.type === 'income' ? '+' : '-'
                                }}{{
                                    formatCurrency(Number(transaction.amount))
                                }}
                            </span>
                        </div>
                    </div>

                    <!-- Desktop -->
                    <div class="hidden md:block">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Tanggal</TableHead>
                                    <TableHead>Deskripsi</TableHead>
                                    <TableHead>Kategori</TableHead>
                                    <TableHead>Tipe</TableHead>
                                    <TableHead class="text-right"
                                        >Jumlah</TableHead
                                    >
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow
                                    v-if="props.recentTransactions.length === 0"
                                >
                                    <TableCell
                                        :colspan="5"
                                        class="text-center text-muted-foreground"
                                    >
                                        Belum ada transaksi.
                                    </TableCell>
                                </TableRow>
                                <TableRow
                                    v-for="transaction in props.recentTransactions"
                                    :key="transaction.id"
                                >
                                    <TableCell class="whitespace-nowrap">{{
                                        formatDate(transaction.date)
                                    }}</TableCell>
                                    <TableCell class="font-medium">{{
                                        transaction.description
                                    }}</TableCell>
                                    <TableCell>
                                        <div
                                            v-if="transaction.category"
                                            class="flex items-center gap-2"
                                        >
                                            <span
                                                class="size-3 rounded-full"
                                                :style="{
                                                    backgroundColor:
                                                        transaction.category
                                                            .color,
                                                }"
                                            />
                                            {{ transaction.category.name }}
                                        </div>
                                    </TableCell>
                                    <TableCell>
                                        <Badge
                                            :variant="
                                                transaction.type === 'income'
                                                    ? 'default'
                                                    : 'destructive'
                                            "
                                        >
                                            {{
                                                transaction.type === 'income'
                                                    ? 'Pemasukan'
                                                    : 'Pengeluaran'
                                            }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell
                                        class="text-right font-semibold"
                                        :class="
                                            transaction.type === 'income'
                                                ? 'text-green-600'
                                                : 'text-red-600'
                                        "
                                    >
                                        {{
                                            transaction.type === 'income'
                                                ? '+'
                                                : '-'
                                        }}{{
                                            formatCurrency(
                                                Number(transaction.amount),
                                            )
                                        }}
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
