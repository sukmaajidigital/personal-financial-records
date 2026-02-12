<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ArrowDownCircle, ArrowUpCircle, ChevronLeft, ChevronRight, FolderPlus, Pencil, Plus, Search, Trash2, X } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Category, PaginatedData, Transaction, TransactionFilters } from '@/types';

type Props = {
    transactions: PaginatedData<Transaction>;
    categories: Pick<Category, 'id' | 'name' | 'color'>[];
    filters: TransactionFilters;
};

const props = defineProps<Props>();
const page = usePage();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Transaksi', href: '/transactions' },
];

const search = ref(props.filters.search ?? '');
const type = ref(props.filters.type || 'all');
const categoryId = ref(props.filters.category_id || 'all');
const dateFrom = ref(props.filters.date_from ?? '');
const dateTo = ref(props.filters.date_to ?? '');

const showDeleteDialog = ref(false);
const deletingTransaction = ref<Transaction | null>(null);

let searchTimeout: ReturnType<typeof setTimeout>;

watch(search, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => applyFilters(), 300);
});

function applyFilters() {
    router.get(
        '/transactions',
        {
            search: search.value || undefined,
            type: type.value === 'all' ? undefined : type.value || undefined,
            category_id: categoryId.value === 'all' ? undefined : categoryId.value || undefined,
            date_from: dateFrom.value || undefined,
            date_to: dateTo.value || undefined,
        },
        { preserveState: true, replace: true },
    );
}

function clearFilters() {
    search.value = '';
    type.value = 'all';
    categoryId.value = 'all';
    dateFrom.value = '';
    dateTo.value = '';
    router.get('/transactions', {}, { preserveState: true, replace: true });
}

function openDelete(transaction: Transaction) {
    deletingTransaction.value = transaction;
    showDeleteDialog.value = true;
}

function submitDelete() {
    if (!deletingTransaction.value) return;
    router.delete(`/transactions/${deletingTransaction.value.id}`, {
        onSuccess: () => {
            showDeleteDialog.value = false;
            deletingTransaction.value = null;
        },
    });
}

function formatCurrency(amount: string | number): string {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(Number(amount));
}

function formatDate(date: string): string {
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    }).format(new Date(date));
}

const hasFilters = () =>
    search.value || (type.value && type.value !== 'all') || (categoryId.value && categoryId.value !== 'all') || dateFrom.value || dateTo.value;
</script>

<template>
    <Head title="Transaksi" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4 md:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <Heading title="Transaksi" description="Kelola semua transaksi keuangan Anda." />
                <div class="flex gap-2">
                    <Button as-child variant="outline">
                        <Link href="/categories">
                            <FolderPlus class="mr-2 size-4" />
                            Tambah Kategori
                        </Link>
                    </Button>
                    <Button as-child>
                        <Link href="/transactions/create">
                            <Plus class="mr-2 size-4" />
                            Tambah Transaksi
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- Success message -->
            <div v-if="page.props.flash && (page.props.flash as Record<string, string>).success" class="rounded-md border border-green-500/50 bg-green-50 p-3 text-sm text-green-700 dark:bg-green-950/20 dark:text-green-400">
                {{ (page.props.flash as Record<string, string>).success }}
            </div>

            <!-- Filters -->
            <Card>
                <CardContent class="p-4">
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                        <div class="relative sm:col-span-2 lg:col-span-1">
                            <Search class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                            <Input v-model="search" placeholder="Cari deskripsi..." class="pl-9" />
                        </div>
                        <Select v-model="type" @update:model-value="applyFilters()">
                            <SelectTrigger>
                                <SelectValue placeholder="Semua tipe" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">Semua tipe</SelectItem>
                                <SelectItem value="income">Pemasukan</SelectItem>
                                <SelectItem value="expense">Pengeluaran</SelectItem>
                            </SelectContent>
                        </Select>
                        <Select v-model="categoryId" @update:model-value="applyFilters()">
                            <SelectTrigger>
                                <SelectValue placeholder="Semua kategori" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">Semua kategori</SelectItem>
                                <SelectItem v-for="cat in props.categories" :key="cat.id" :value="String(cat.id)">
                                    {{ cat.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <Input v-model="dateFrom" type="date" placeholder="Dari tanggal" @change="applyFilters()" />
                        <Input v-model="dateTo" type="date" placeholder="Sampai tanggal" @change="applyFilters()" />
                    </div>
                    <div v-if="hasFilters()" class="mt-3 flex justify-end">
                        <Button variant="ghost" size="sm" @click="clearFilters()">
                            <X class="mr-1 size-4" />
                            Reset Filter
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <!-- Transactions List -->
            <Card>
                <CardContent class="p-0">
                    <!-- Mobile cards -->
                    <div class="block md:hidden">
                        <div v-if="props.transactions.data.length === 0" class="p-6 text-center text-sm text-muted-foreground">
                            Tidak ada transaksi ditemukan.
                        </div>
                        <div
                            v-for="transaction in props.transactions.data"
                            :key="transaction.id"
                            class="flex items-center justify-between border-b p-4 last:border-b-0"
                        >
                            <div class="flex items-center gap-3 overflow-hidden">
                                <div class="flex size-9 shrink-0 items-center justify-center rounded-full" :class="transaction.type === 'income' ? 'bg-green-100 dark:bg-green-950' : 'bg-red-100 dark:bg-red-950'">
                                    <ArrowUpCircle v-if="transaction.type === 'income'" class="size-5 text-green-600" />
                                    <ArrowDownCircle v-else class="size-5 text-red-600" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate font-medium">{{ transaction.description }}</p>
                                    <div class="flex items-center gap-2 text-xs text-muted-foreground">
                                        <span v-if="transaction.category" class="inline-flex items-center gap-1">
                                            <span class="inline-block size-2 rounded-full" :style="{ backgroundColor: transaction.category.color }" />
                                            {{ transaction.category.name }}
                                        </span>
                                        <span>{{ formatDate(transaction.date) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="whitespace-nowrap text-sm font-semibold" :class="transaction.type === 'income' ? 'text-green-600' : 'text-red-600'">
                                    {{ transaction.type === 'income' ? '+' : '-' }}{{ formatCurrency(transaction.amount) }}
                                </span>
                                <div class="flex shrink-0">
                                    <Button variant="ghost" size="icon" as-child>
                                        <Link :href="`/transactions/${transaction.id}/edit`">
                                            <Pencil class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button variant="ghost" size="icon" @click="openDelete(transaction)">
                                        <Trash2 class="size-4 text-destructive" />
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Desktop table -->
                    <div class="hidden md:block">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Tanggal</TableHead>
                                    <TableHead>Deskripsi</TableHead>
                                    <TableHead>Kategori</TableHead>
                                    <TableHead>Tipe</TableHead>
                                    <TableHead class="text-right">Jumlah</TableHead>
                                    <TableHead class="w-28 text-right">Aksi</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-if="props.transactions.data.length === 0">
                                    <TableCell :colspan="6" class="text-center text-muted-foreground">
                                        Tidak ada transaksi ditemukan.
                                    </TableCell>
                                </TableRow>
                                <TableRow v-for="transaction in props.transactions.data" :key="transaction.id">
                                    <TableCell class="whitespace-nowrap text-sm">{{ formatDate(transaction.date) }}</TableCell>
                                    <TableCell class="max-w-xs truncate font-medium">{{ transaction.description }}</TableCell>
                                    <TableCell>
                                        <div v-if="transaction.category" class="flex items-center gap-2">
                                            <span class="size-3 rounded-full" :style="{ backgroundColor: transaction.category.color }" />
                                            <span class="text-sm">{{ transaction.category.name }}</span>
                                        </div>
                                    </TableCell>
                                    <TableCell>
                                        <Badge :variant="transaction.type === 'income' ? 'default' : 'destructive'">
                                            {{ transaction.type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="text-right font-semibold" :class="transaction.type === 'income' ? 'text-green-600' : 'text-red-600'">
                                        {{ transaction.type === 'income' ? '+' : '-' }}{{ formatCurrency(transaction.amount) }}
                                    </TableCell>
                                    <TableCell class="text-right">
                                        <div class="flex justify-end gap-1">
                                            <Button variant="ghost" size="icon" as-child>
                                                <Link :href="`/transactions/${transaction.id}/edit`">
                                                    <Pencil class="size-4" />
                                                </Link>
                                            </Button>
                                            <Button variant="ghost" size="icon" @click="openDelete(transaction)">
                                                <Trash2 class="size-4 text-destructive" />
                                            </Button>
                                        </div>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="props.transactions.last_page > 1" class="flex items-center justify-between border-t px-4 py-3">
                        <p class="text-sm text-muted-foreground">
                            Menampilkan {{ props.transactions.from }}-{{ props.transactions.to }} dari {{ props.transactions.total }} transaksi
                        </p>
                        <div class="flex gap-1">
                            <Button
                                v-for="link in props.transactions.links"
                                :key="link.label"
                                variant="outline"
                                size="sm"
                                :disabled="!link.url || link.active"
                                :class="link.active ? 'bg-primary text-primary-foreground' : ''"
                                @click="link.url && router.get(link.url, {}, { preserveState: true })"
                            >
                                <span v-html="link.label" />
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:open="showDeleteDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Hapus Transaksi</DialogTitle>
                    <DialogDescription>
                        Apakah Anda yakin ingin menghapus transaksi
                        <strong>{{ deletingTransaction?.description }}</strong>?
                        Tindakan ini tidak dapat dibatalkan.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <DialogClose as-child>
                        <Button variant="outline">Batal</Button>
                    </DialogClose>
                    <Button variant="destructive" @click="submitDelete">Hapus</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
