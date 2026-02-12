<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardFooter,
    CardHeader,
} from '@/components/ui/card';
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
import type { BreadcrumbItem, Category } from '@/types';

type Props = {
    categories: Pick<Category, 'id' | 'name' | 'color'>[];
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Transaksi', href: '/transactions' },
    { title: 'Tambah Transaksi', href: '/transactions/create' },
];

const form = useForm({
    category_id: '',
    description: '',
    amount: '',
    type: 'expense' as 'income' | 'expense',
    date: new Date().toISOString().split('T')[0],
});

function submit() {
    form.post('/transactions');
}
</script>

<template>
    <Head title="Tambah Transaksi" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4 md:p-6">
            <div class="flex items-center gap-4">
                <Button variant="ghost" size="icon" as-child>
                    <Link href="/transactions">
                        <ArrowLeft class="size-4" />
                    </Link>
                </Button>
                <Heading
                    title="Tambah Transaksi"
                    description="Catat transaksi baru."
                />
            </div>

            <Card class="mx-auto w-full max-w-2xl">
                <form @submit.prevent="submit">
                    <CardContent class="space-y-4 pt-6">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="type">Tipe Transaksi</Label>
                                <Select v-model="form.type">
                                    <SelectTrigger id="type">
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
                                <InputError :message="form.errors.type" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="category">Kategori</Label>
                                <Select v-model="form.category_id">
                                    <SelectTrigger id="category">
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
                                            {{ cat.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError
                                    :message="form.errors.category_id"
                                />
                            </div>
                        </div>

                        <div class="grid gap-2">
                            <Label for="description">Deskripsi</Label>
                            <Input
                                id="description"
                                v-model="form.description"
                                placeholder="Contoh: Makan siang"
                                required
                            />
                            <InputError :message="form.errors.description" />
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="amount">Jumlah (Rp)</Label>
                                <Input
                                    id="amount"
                                    v-model="form.amount"
                                    type="number"
                                    min="0.01"
                                    step="0.01"
                                    placeholder="0"
                                    required
                                />
                                <InputError :message="form.errors.amount" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="date">Tanggal</Label>
                                <Input
                                    id="date"
                                    v-model="form.date"
                                    type="date"
                                    required
                                />
                                <InputError :message="form.errors.date" />
                            </div>
                        </div>
                    </CardContent>

                    <CardFooter class="flex justify-end gap-2">
                        <Button variant="outline" as-child>
                            <Link href="/transactions">Batal</Link>
                        </Button>
                        <Button type="submit" :disabled="form.processing"
                            >Simpan Transaksi</Button
                        >
                    </CardFooter>
                </form>
            </Card>
        </div>
    </AppLayout>
</template>
