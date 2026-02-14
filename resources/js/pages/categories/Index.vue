<script setup lang="ts">
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Category } from '@/types';

type Props = {
    categories: Category[];
};

const props = defineProps<Props>();
const page = usePage();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Kategori', href: '/categories' },
];

const showCreateDialog = ref(false);
const showEditDialog = ref(false);
const showDeleteDialog = ref(false);
const editingCategory = ref<Category | null>(null);
const deletingCategory = ref<Category | null>(null);

const createForm = useForm({
    name: '',
    color: '#3b82f6',
});

const editForm = useForm({
    name: '',
    color: '#3b82f6',
});

function submitCreate() {
    createForm.post('/categories', {
        onSuccess: () => {
            showCreateDialog.value = false;
            createForm.reset();
        },
    });
}

function openEdit(category: Category) {
    editingCategory.value = category;
    editForm.name = category.name;
    editForm.color = category.color;
    showEditDialog.value = true;
}

function submitEdit() {
    if (!editingCategory.value) return;
    editForm.put(`/categories/${editingCategory.value.id}`, {
        onSuccess: () => {
            showEditDialog.value = false;
            editingCategory.value = null;
        },
    });
}

function openDelete(category: Category) {
    deletingCategory.value = category;
    showDeleteDialog.value = true;
}

function submitDelete() {
    if (!deletingCategory.value) return;
    router.delete(`/categories/${deletingCategory.value.id}`, {
        onSuccess: () => {
            showDeleteDialog.value = false;
            deletingCategory.value = null;
        },
    });
}
</script>

<template>
    <Head title="Kategori" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4 md:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <Heading title="Kategori" description="Kelola kategori untuk transaksi keuangan Anda." />

                <Dialog v-model:open="showCreateDialog">
                    <DialogTrigger as-child>
                        <Button>
                            <Plus class="mr-2 size-4" />
                            Tambah Kategori
                        </Button>
                    </DialogTrigger>
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>Tambah Kategori Baru</DialogTitle>
                            <DialogDescription>
                                Buat kategori baru untuk mengelompokkan transaksi Anda.
                            </DialogDescription>
                        </DialogHeader>
                        <form @submit.prevent="submitCreate" class="space-y-4">
                            <div class="grid gap-2">
                                <Label for="create-name">Nama Kategori</Label>
                                <Input id="create-name" v-model="createForm.name" placeholder="Contoh: Makanan" required />
                                <InputError :message="createForm.errors.name" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="create-color">Warna</Label>
                                <div class="flex items-center gap-3">
                                    <input id="create-color" v-model="createForm.color" type="color" class="h-10 w-14 cursor-pointer rounded-md border" />
                                    <Input v-model="createForm.color" class="flex-1 font-mono" maxlength="7" />
                                </div>
                                <InputError :message="createForm.errors.color" />
                            </div>
                            <DialogFooter>
                                <DialogClose as-child>
                                    <Button type="button" variant="outline">Batal</Button>
                                </DialogClose>
                                <Button type="submit" :disabled="createForm.processing">Simpan</Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>
            </div>

            <!-- Error display -->
            <div v-if="page.props.errors && (page.props.errors as Record<string, string>).category" class="rounded-md border border-destructive/50 bg-destructive/10 p-3 text-sm text-destructive">
                {{ (page.props.errors as Record<string, string>).category }}
            </div>

            <Card>
                <CardContent class="p-0">
                    <!-- Mobile cards -->
                    <div class="block md:hidden">
                        <div v-if="props.categories.length === 0" class="p-6 text-center text-sm text-muted-foreground">
                            Belum ada kategori. Tambahkan kategori pertama Anda!
                        </div>
                        <div v-for="category in props.categories" :key="category.id" class="flex items-center justify-between border-b p-4 last:border-b-0">
                            <div class="flex items-center gap-3">
                                <div class="size-4 rounded-full" :style="{ backgroundColor: category.color }" />
                                <div>
                                    <p class="font-medium">{{ category.name }}</p>
                                    <p class="text-xs text-muted-foreground">{{ category.transactions_count ?? 0 }} transaksi</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1">
                                <Button variant="ghost" size="icon" @click="openEdit(category)">
                                    <Pencil class="size-4" />
                                </Button>
                                <Button variant="ghost" size="icon" @click="openDelete(category)">
                                    <Trash2 class="size-4 text-destructive" />
                                </Button>
                            </div>
                        </div>
                    </div>

                    <!-- Desktop table -->
                    <div class="hidden md:block">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="w-12">Warna</TableHead>
                                    <TableHead>Nama</TableHead>
                                    <TableHead class="text-center">Jumlah Transaksi</TableHead>
                                    <TableHead class="w-28 text-right">Aksi</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-if="props.categories.length === 0">
                                    <TableCell :colspan="4" class="text-center text-muted-foreground">
                                        Belum ada kategori. Tambahkan kategori pertama Anda!
                                    </TableCell>
                                </TableRow>
                                <TableRow v-for="category in props.categories" :key="category.id">
                                    <TableCell>
                                        <div class="size-5 rounded-full" :style="{ backgroundColor: category.color }" />
                                    </TableCell>
                                    <TableCell class="font-medium">{{ category.name }}</TableCell>
                                    <TableCell class="text-center">
                                        <Badge variant="secondary">{{ category.transactions_count ?? 0 }}</Badge>
                                    </TableCell>
                                    <TableCell class="text-right">
                                        <div class="flex justify-end gap-1">
                                            <Button variant="ghost" size="icon" @click="openEdit(category)">
                                                <Pencil class="size-4" />
                                            </Button>
                                            <Button variant="ghost" size="icon" @click="openDelete(category)">
                                                <Trash2 class="size-4 text-destructive" />
                                            </Button>
                                        </div>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Edit Dialog -->
        <Dialog v-model:open="showEditDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Edit Kategori</DialogTitle>
                    <DialogDescription>Perbarui informasi kategori.</DialogDescription>
                </DialogHeader>
                <form @submit.prevent="submitEdit" class="space-y-4">
                    <div class="grid gap-2">
                        <Label for="edit-name">Nama Kategori</Label>
                        <Input id="edit-name" v-model="editForm.name" placeholder="Contoh: Makanan" required />
                        <InputError :message="editForm.errors.name" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="edit-color">Warna</Label>
                        <div class="flex items-center gap-3">
                            <input id="edit-color" v-model="editForm.color" type="color" class="h-10 w-14 cursor-pointer rounded-md border" />
                            <Input v-model="editForm.color" class="flex-1 font-mono" maxlength="7" />
                        </div>
                        <InputError :message="editForm.errors.color" />
                    </div>
                    <DialogFooter>
                        <DialogClose as-child>
                            <Button type="button" variant="outline">Batal</Button>
                        </DialogClose>
                        <Button type="submit" :disabled="editForm.processing">Perbarui</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:open="showDeleteDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Hapus Kategori</DialogTitle>
                    <DialogDescription>
                        Apakah Anda yakin ingin menghapus kategori
                        <strong>{{ deletingCategory?.name }}</strong>? Tindakan ini tidak dapat dibatalkan.
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
