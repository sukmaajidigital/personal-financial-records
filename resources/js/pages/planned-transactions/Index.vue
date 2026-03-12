<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import {
    Check,
    ClipboardList,
    Edit,
    Filter,
    Plus,
    Send,
    Trash2,
} from 'lucide-vue-next';
import { ref } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
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
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import type {
    BreadcrumbItem,
    Category,
    PaginatedData,
    PlannedTransaction,
} from '@/types';

type Props = {
    plannedTransactions: PaginatedData<PlannedTransaction>;
    categories: Pick<Category, 'id' | 'name' | 'color'>[];
    filters: { status?: string; type?: string };
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Perencanaan', href: '/planned-transactions' },
];

// --- Filters ---
const statusFilter = ref(props.filters.status || '');
const typeFilter = ref(props.filters.type || '');

function applyFilters() {
    const params: Record<string, string> = {};
    if (statusFilter.value) params.status = statusFilter.value;
    if (typeFilter.value) params.type = typeFilter.value;
    router.get('/planned-transactions', params, { preserveState: true });
}

function resetFilters() {
    statusFilter.value = '';
    typeFilter.value = '';
    router.get('/planned-transactions', {}, { preserveState: false });
}

// --- Delete confirmation ---
const deleteTarget = ref<PlannedTransaction | null>(null);
const showDeleteDialog = ref(false);

function confirmDelete(item: PlannedTransaction) {
    deleteTarget.value = item;
    showDeleteDialog.value = true;
}

function executeDelete() {
    if (!deleteTarget.value) return;
    router.delete(`/planned-transactions/${deleteTarget.value.id}`, {
        onSuccess: () => {
            showDeleteDialog.value = false;
            deleteTarget.value = null;
        },
    });
}

// --- Post review modal ---
const showPostDialog = ref(false);
const postTarget = ref<PlannedTransaction | null>(null);
const postForm = useForm({
    category_id: '',
    description: '',
    amount: '',
    type: 'expense' as 'income' | 'expense',
    date: '',
});

function openPostModal(item: PlannedTransaction) {
    postTarget.value = item;
    postForm.category_id = String(item.category_id);
    postForm.description = item.description;
    postForm.amount = item.amount;
    postForm.type = item.type;
    postForm.date = item.planned_date;
    showPostDialog.value = true;
}

function submitPost() {
    if (!postTarget.value) return;
    postForm.post(`/planned-transactions/${postTarget.value.id}/post`, {
        onSuccess: () => {
            showPostDialog.value = false;
            postTarget.value = null;
            postForm.reset();
        },
    });
}

// --- Helpers ---
function formatCurrency(amount: number | string): string {
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
</script>

<template>
    <Head title="Perencanaan Transaksi" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4 md:p-6">
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <Heading
                    title="Perencanaan Transaksi"
                    description="Kelola rencana transaksi pemasukan dan pengeluaran."
                />
                <Button as-child>
                    <Link href="/planned-transactions/create">
                        <Plus class="mr-1.5 size-4" />
                        Tambah Rencana
                    </Link>
                </Button>
            </div>

            <!-- Filters -->
            <Card>
                <CardContent class="pt-6">
                    <div class="grid gap-3 sm:grid-cols-3">
                        <div>
                            <label
                                class="mb-1.5 block text-xs font-medium text-muted-foreground"
                                >Status</label
                            >
                            <Select v-model="statusFilter">
                                <SelectTrigger
                                    ><SelectValue placeholder="Semua status"
                                /></SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="">Semua</SelectItem>
                                    <SelectItem value="draft">Draft</SelectItem>
                                    <SelectItem value="posted"
                                        >Posted</SelectItem
                                    >
                                </SelectContent>
                            </Select>
                        </div>
                        <div>
                            <label
                                class="mb-1.5 block text-xs font-medium text-muted-foreground"
                                >Tipe</label
                            >
                            <Select v-model="typeFilter">
                                <SelectTrigger
                                    ><SelectValue placeholder="Semua tipe"
                                /></SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="">Semua</SelectItem>
                                    <SelectItem value="income"
                                        >Pemasukan</SelectItem
                                    >
                                    <SelectItem value="expense"
                                        >Pengeluaran</SelectItem
                                    >
                                </SelectContent>
                            </Select>
                        </div>
                        <div class="flex items-end gap-2">
                            <Button @click="applyFilters" class="flex-1">
                                <Filter class="mr-1.5 size-3.5" />
                                Terapkan
                            </Button>
                            <Button variant="outline" @click="resetFilters"
                                >Reset</Button
                            >
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Table -->
            <Card>
                <CardContent class="p-0">
                    <div
                        v-if="props.plannedTransactions.data.length === 0"
                        class="flex h-48 items-center justify-center text-sm text-muted-foreground"
                    >
                        <div class="text-center">
                            <ClipboardList
                                class="mx-auto mb-2 size-10 text-muted-foreground/50"
                            />
                            <p>Belum ada rencana transaksi.</p>
                        </div>
                    </div>
                    <div v-else class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b bg-muted/50">
                                    <th
                                        class="px-4 py-3 text-left font-medium text-muted-foreground"
                                    >
                                        Tanggal
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left font-medium text-muted-foreground"
                                    >
                                        Deskripsi
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left font-medium text-muted-foreground"
                                    >
                                        Kategori
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left font-medium text-muted-foreground"
                                    >
                                        Tipe
                                    </th>
                                    <th
                                        class="px-4 py-3 text-right font-medium text-muted-foreground"
                                    >
                                        Jumlah
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center font-medium text-muted-foreground"
                                    >
                                        Status
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center font-medium text-muted-foreground"
                                    >
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="item in props.plannedTransactions
                                        .data"
                                    :key="item.id"
                                    class="border-b transition-colors hover:bg-muted/30"
                                >
                                    <td class="px-4 py-3 text-muted-foreground">
                                        {{ formatDate(item.planned_date) }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div>
                                            <p class="font-medium">
                                                {{ item.description }}
                                            </p>
                                            <p
                                                v-if="item.notes"
                                                class="mt-0.5 text-xs text-muted-foreground"
                                            >
                                                {{ item.notes }}
                                            </p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div
                                            v-if="item.category"
                                            class="flex items-center gap-2"
                                        >
                                            <span
                                                class="size-2.5 rounded-full"
                                                :style="{
                                                    backgroundColor:
                                                        item.category.color,
                                                }"
                                            />
                                            <span>{{
                                                item.category.name
                                            }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <Badge
                                            :variant="
                                                item.type === 'income'
                                                    ? 'default'
                                                    : 'destructive'
                                            "
                                            class="text-xs"
                                        >
                                            {{
                                                item.type === 'income'
                                                    ? 'Pemasukan'
                                                    : 'Pengeluaran'
                                            }}
                                        </Badge>
                                    </td>
                                    <td
                                        class="px-4 py-3 text-right font-medium"
                                        :class="
                                            item.type === 'income'
                                                ? 'text-green-600'
                                                : 'text-red-600'
                                        "
                                    >
                                        {{ item.type === 'income' ? '+' : '-'
                                        }}{{ formatCurrency(item.amount) }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <Badge
                                            v-if="item.status === 'draft'"
                                            variant="outline"
                                            class="text-xs"
                                        >
                                            Draft
                                        </Badge>
                                        <Badge
                                            v-else
                                            class="bg-green-100 text-xs text-green-700 dark:bg-green-950 dark:text-green-400"
                                        >
                                            <Check class="mr-1 size-3" />
                                            Posted
                                        </Badge>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div
                                            class="flex items-center justify-center gap-1"
                                        >
                                            <template
                                                v-if="item.status === 'draft'"
                                            >
                                                <Button
                                                    size="sm"
                                                    variant="default"
                                                    @click="openPostModal(item)"
                                                    title="Post ke transaksi"
                                                >
                                                    <Send class="size-3.5" />
                                                </Button>
                                                <Button
                                                    size="sm"
                                                    variant="outline"
                                                    as-child
                                                >
                                                    <Link
                                                        :href="`/planned-transactions/${item.id}/edit`"
                                                    >
                                                        <Edit
                                                            class="size-3.5"
                                                        />
                                                    </Link>
                                                </Button>
                                                <Button
                                                    size="sm"
                                                    variant="destructive"
                                                    @click="confirmDelete(item)"
                                                >
                                                    <Trash2 class="size-3.5" />
                                                </Button>
                                            </template>
                                            <span
                                                v-else
                                                class="text-xs text-muted-foreground"
                                            >
                                                {{
                                                    item.posted_at
                                                        ? formatDate(
                                                              item.posted_at,
                                                          )
                                                        : ''
                                                }}
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div
                        v-if="props.plannedTransactions.last_page > 1"
                        class="flex items-center justify-between border-t px-4 py-3"
                    >
                        <p class="text-sm text-muted-foreground">
                            {{ props.plannedTransactions.from }}-{{
                                props.plannedTransactions.to
                            }}
                            dari {{ props.plannedTransactions.total }}
                        </p>
                        <div class="flex gap-1">
                            <template
                                v-for="link in props.plannedTransactions.links"
                                :key="link.label"
                            >
                                <Button
                                    v-if="link.url"
                                    size="sm"
                                    :variant="
                                        link.active ? 'default' : 'outline'
                                    "
                                    as-child
                                >
                                    <Link :href="link.url">{{
                                        link.label
                                    }}</Link>
                                </Button>
                                <Button
                                    v-else
                                    size="sm"
                                    variant="outline"
                                    disabled
                                >
                                    {{ link.label }}
                                </Button>
                            </template>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:open="showDeleteDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Hapus Rencana Transaksi</DialogTitle>
                    <DialogDescription>
                        Apakah Anda yakin ingin menghapus rencana "{{
                            deleteTarget?.description
                        }}"? Tindakan ini tidak dapat dibatalkan.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <DialogClose as-child>
                        <Button variant="outline">Batal</Button>
                    </DialogClose>
                    <Button variant="destructive" @click="executeDelete"
                        >Hapus</Button
                    >
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Post Review Modal -->
        <Dialog v-model:open="showPostDialog">
            <DialogContent class="sm:max-w-lg">
                <DialogHeader>
                    <DialogTitle>Review & Post Transaksi</DialogTitle>
                    <DialogDescription>
                        Tinjau dan sesuaikan data sebelum memposting ke
                        transaksi. Anda dapat mengubah data jika diperlukan.
                    </DialogDescription>
                </DialogHeader>
                <form @submit.prevent="submitPost">
                    <div class="space-y-4 py-4">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="post-type">Tipe</Label>
                                <Select v-model="postForm.type">
                                    <SelectTrigger id="post-type">
                                        <SelectValue placeholder="Pilih tipe" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="income"
                                            >Pemasukan</SelectItem
                                        >
                                        <SelectItem value="expense"
                                            >Pengeluaran</SelectItem
                                        >
                                    </SelectContent>
                                </Select>
                                <InputError :message="postForm.errors.type" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="post-category">Kategori</Label>
                                <Select v-model="postForm.category_id">
                                    <SelectTrigger id="post-category">
                                        <SelectValue
                                            placeholder="Pilih kategori"
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="cat in props.categories"
                                            :key="cat.id"
                                            :value="String(cat.id)"
                                        >
                                            <span
                                                class="inline-flex items-center gap-2"
                                            >
                                                <span
                                                    class="size-2.5 shrink-0 rounded-full"
                                                    :style="{
                                                        backgroundColor:
                                                            cat.color,
                                                    }"
                                                />
                                                {{ cat.name }}
                                            </span>
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError
                                    :message="postForm.errors.category_id"
                                />
                            </div>
                        </div>

                        <div class="grid gap-2">
                            <Label for="post-description">Deskripsi</Label>
                            <Input
                                id="post-description"
                                v-model="postForm.description"
                                required
                            />
                            <InputError
                                :message="postForm.errors.description"
                            />
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="post-amount">Jumlah (Rp)</Label>
                                <Input
                                    id="post-amount"
                                    v-model="postForm.amount"
                                    type="number"
                                    min="0.01"
                                    step="0.01"
                                    required
                                />
                                <InputError :message="postForm.errors.amount" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="post-date">Tanggal Transaksi</Label>
                                <Input
                                    id="post-date"
                                    v-model="postForm.date"
                                    type="date"
                                    required
                                />
                                <InputError :message="postForm.errors.date" />
                            </div>
                        </div>
                    </div>
                    <DialogFooter>
                        <DialogClose as-child>
                            <Button type="button" variant="outline"
                                >Batal</Button
                            >
                        </DialogClose>
                        <Button type="submit" :disabled="postForm.processing">
                            <Send class="mr-1.5 size-3.5" />
                            Post ke Transaksi
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
