<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Filter, Lightbulb, Plus, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';
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
    PaginatedData,
    Suggestion,
    SuggestionFocus,
} from '@/types';

type Props = {
    suggestions: PaginatedData<Suggestion>;
    filters: { focus?: string; status?: string };
    authUserId: number;
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Saran', href: '/suggestions' },
];

const focusFilter = ref(props.filters.focus || '');
const statusFilter = ref(props.filters.status || '');

function applyFilters() {
    const params: Record<string, string> = {};
    if (focusFilter.value) params.focus = focusFilter.value;
    if (statusFilter.value) params.status = statusFilter.value;
    router.get('/suggestions', params, { preserveState: true });
}

function resetFilters() {
    focusFilter.value = '';
    statusFilter.value = '';
    router.get('/suggestions', {}, { preserveState: false });
}

// --- Delete ---
const deleteTarget = ref<Suggestion | null>(null);
const showDeleteDialog = ref(false);

function confirmDelete(item: Suggestion) {
    deleteTarget.value = item;
    showDeleteDialog.value = true;
}

function executeDelete() {
    if (!deleteTarget.value) return;
    router.delete(`/suggestions/${deleteTarget.value.id}`, {
        onSuccess: () => {
            showDeleteDialog.value = false;
            deleteTarget.value = null;
        },
    });
}

// --- Image preview ---
const previewImage = ref<string | null>(null);
const showImageDialog = ref(false);

function openImage(url: string) {
    previewImage.value = url;
    showImageDialog.value = true;
}

// --- Helpers ---
const focusLabels: Record<SuggestionFocus, string> = {
    fitur_baru: 'Fitur Baru',
    pengurangan_fitur: 'Pengurangan Fitur',
    tampilan: 'Perubahan Tampilan',
    performa: 'Performa',
    keamanan: 'Keamanan',
    lainnya: 'Lainnya',
};

const focusColors: Record<SuggestionFocus, string> = {
    fitur_baru: 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-400',
    pengurangan_fitur:
        'bg-orange-100 text-orange-700 dark:bg-orange-950 dark:text-orange-400',
    tampilan:
        'bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-400',
    performa:
        'bg-green-100 text-green-700 dark:bg-green-950 dark:text-green-400',
    keamanan: 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-400',
    lainnya: 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
};

const statusLabels: Record<string, string> = {
    pending: 'Menunggu',
    reviewed: 'Ditinjau',
    accepted: 'Diterima',
    rejected: 'Ditolak',
};

const statusColors: Record<string, string> = {
    pending:
        'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-400',
    reviewed: 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-400',
    accepted:
        'bg-green-100 text-green-700 dark:bg-green-950 dark:text-green-400',
    rejected: 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-400',
};

function formatDate(date: string): string {
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    }).format(new Date(date));
}
</script>

<template>
    <Head title="Saran" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4 md:p-6">
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">
                        Saran & Masukan
                    </h1>
                    <p class="text-sm text-muted-foreground">
                        Berikan saran untuk pengembangan aplikasi.
                    </p>
                </div>
                <Button as-child>
                    <Link href="/suggestions/create">
                        <Plus class="mr-1.5 size-4" />
                        Buat Saran
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
                                >Fokus</label
                            >
                            <Select v-model="focusFilter">
                                <SelectTrigger
                                    ><SelectValue placeholder="Semua fokus"
                                /></SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="">Semua</SelectItem>
                                    <SelectItem value="fitur_baru"
                                        >Fitur Baru</SelectItem
                                    >
                                    <SelectItem value="pengurangan_fitur"
                                        >Pengurangan Fitur</SelectItem
                                    >
                                    <SelectItem value="tampilan"
                                        >Perubahan Tampilan</SelectItem
                                    >
                                    <SelectItem value="performa"
                                        >Performa</SelectItem
                                    >
                                    <SelectItem value="keamanan"
                                        >Keamanan</SelectItem
                                    >
                                    <SelectItem value="lainnya"
                                        >Lainnya</SelectItem
                                    >
                                </SelectContent>
                            </Select>
                        </div>
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
                                    <SelectItem value="pending"
                                        >Menunggu</SelectItem
                                    >
                                    <SelectItem value="reviewed"
                                        >Ditinjau</SelectItem
                                    >
                                    <SelectItem value="accepted"
                                        >Diterima</SelectItem
                                    >
                                    <SelectItem value="rejected"
                                        >Ditolak</SelectItem
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

            <!-- Suggestions Grid -->
            <div
                v-if="props.suggestions.data.length === 0"
                class="flex h-64 items-center justify-center"
            >
                <div class="text-center">
                    <Lightbulb
                        class="mx-auto mb-3 size-12 text-muted-foreground/30"
                    />
                    <p class="text-muted-foreground">
                        Belum ada saran. Jadilah yang pertama!
                    </p>
                </div>
            </div>

            <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <Card
                    v-for="item in props.suggestions.data"
                    :key="item.id"
                    class="flex flex-col transition-shadow hover:shadow-md"
                >
                    <!-- Image -->
                    <div
                        v-if="item.image"
                        class="relative cursor-pointer overflow-hidden rounded-t-lg"
                        @click="openImage(`/storage/${item.image}`)"
                    >
                        <img
                            :src="`/storage/${item.image}`"
                            :alt="item.title"
                            class="h-48 w-full object-cover transition-transform hover:scale-105"
                        />
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"
                        />
                    </div>

                    <CardContent
                        class="flex flex-1 flex-col gap-3 pt-4"
                        :class="{ 'rounded-t-lg': !item.image }"
                    >
                        <!-- Header -->
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="leading-tight font-semibold">
                                {{ item.title }}
                            </h3>
                            <div class="flex shrink-0 gap-1">
                                <Badge
                                    :class="focusColors[item.focus]"
                                    class="text-[10px]"
                                >
                                    {{ focusLabels[item.focus] }}
                                </Badge>
                            </div>
                        </div>

                        <!-- Description -->
                        <p
                            class="line-clamp-3 flex-1 text-sm text-muted-foreground"
                        >
                            {{ item.description }}
                        </p>

                        <!-- Footer -->
                        <div
                            class="flex items-center justify-between border-t pt-3"
                        >
                            <div class="flex items-center gap-2">
                                <div
                                    class="flex size-6 items-center justify-center rounded-full bg-primary text-[10px] font-bold text-primary-foreground"
                                >
                                    {{
                                        item.user?.name
                                            ?.charAt(0)
                                            ?.toUpperCase() || '?'
                                    }}
                                </div>
                                <div class="text-xs">
                                    <p class="font-medium">
                                        {{ item.user?.name || 'Unknown' }}
                                    </p>
                                    <p class="text-muted-foreground">
                                        {{ formatDate(item.created_at) }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <Badge
                                    :class="statusColors[item.status]"
                                    class="text-[10px]"
                                >
                                    {{ statusLabels[item.status] }}
                                </Badge>
                                <Button
                                    v-if="item.user_id === props.authUserId"
                                    size="sm"
                                    variant="ghost"
                                    @click="confirmDelete(item)"
                                    class="size-7 p-0 text-muted-foreground hover:text-destructive"
                                >
                                    <Trash2 class="size-3.5" />
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Pagination -->
            <div
                v-if="props.suggestions.last_page > 1"
                class="flex items-center justify-center gap-1"
            >
                <template
                    v-for="link in props.suggestions.links"
                    :key="link.label"
                >
                    <Button
                        v-if="link.url"
                        size="sm"
                        :variant="link.active ? 'default' : 'outline'"
                        as-child
                    >
                        <Link :href="link.url"
                            ><span v-html="link.label"
                        /></Link>
                    </Button>
                    <Button v-else size="sm" variant="outline" disabled
                        ><span v-html="link.label"
                    /></Button>
                </template>
            </div>
        </div>

        <!-- Delete Dialog -->
        <Dialog v-model:open="showDeleteDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Hapus Saran</DialogTitle>
                    <DialogDescription>
                        Apakah Anda yakin ingin menghapus saran "{{
                            deleteTarget?.title
                        }}"?
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

        <!-- Image Preview Dialog -->
        <Dialog v-model:open="showImageDialog">
            <DialogContent class="overflow-hidden p-0 sm:max-w-3xl">
                <img
                    v-if="previewImage"
                    :src="previewImage"
                    alt="Preview"
                    class="h-auto w-full"
                />
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
