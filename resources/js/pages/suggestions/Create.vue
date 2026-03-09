<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ArrowLeft, ImagePlus, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardFooter,
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
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Saran', href: '/suggestions' },
    { title: 'Buat Saran', href: '/suggestions/create' },
];

const form = useForm({
    title: '',
    description: '',
    focus: '' as string,
    image: null as File | null,
});

const imagePreview = ref<string | null>(null);

function handleImageChange(event: Event) {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        form.image = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
}

function removeImage() {
    form.image = null;
    imagePreview.value = null;
    // Reset file input
    const input = document.getElementById('image') as HTMLInputElement;
    if (input) input.value = '';
}

function submit() {
    form.post('/suggestions', {
        forceFormData: true,
    });
}

const focusOptions = [
    { value: 'fitur_baru', label: 'Fitur Baru', desc: 'Saran penambahan fitur baru' },
    { value: 'pengurangan_fitur', label: 'Pengurangan Fitur', desc: 'Saran menghapus atau menyederhanakan fitur' },
    { value: 'tampilan', label: 'Perubahan Tampilan', desc: 'Saran perubahan UI/UX' },
    { value: 'performa', label: 'Performa', desc: 'Saran peningkatan kecepatan/efisiensi' },
    { value: 'keamanan', label: 'Keamanan', desc: 'Saran terkait keamanan data' },
    { value: 'lainnya', label: 'Lainnya', desc: 'Saran umum lainnya' },
];
</script>

<template>
    <Head title="Buat Saran" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4 md:p-6">
            <div class="flex items-center gap-4">
                <Button variant="ghost" size="icon" as-child>
                    <Link href="/suggestions">
                        <ArrowLeft class="size-4" />
                    </Link>
                </Button>
                <Heading
                    title="Buat Saran Baru"
                    description="Berikan saran dan masukan untuk pengembangan aplikasi."
                />
            </div>

            <Card class="mx-auto w-full max-w-2xl">
                <form @submit.prevent="submit">
                    <CardContent class="space-y-4 pt-6">
                        <div class="grid gap-2">
                            <Label for="title">Judul Saran</Label>
                            <Input
                                id="title"
                                v-model="form.title"
                                placeholder="Contoh: Tambahkan fitur export ke PDF"
                                required
                            />
                            <InputError :message="form.errors.title" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="focus">Fokus / Kategori</Label>
                            <Select v-model="form.focus">
                                <SelectTrigger id="focus">
                                    <SelectValue placeholder="Pilih fokus saran" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="opt in focusOptions" :key="opt.value" :value="opt.value">
                                        <div>
                                            <span class="font-medium">{{ opt.label }}</span>
                                            <span class="ml-2 text-xs text-muted-foreground">{{ opt.desc }}</span>
                                        </div>
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.focus" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="description">Deskripsi</Label>
                            <Textarea
                                id="description"
                                v-model="form.description"
                                placeholder="Jelaskan saran Anda secara detail..."
                                rows="5"
                                required
                            />
                            <InputError :message="form.errors.description" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="image">Gambar (Opsional)</Label>
                            <p class="text-xs text-muted-foreground">Upload screenshot atau mockup. Maks 2MB.</p>
                            <div class="flex items-center gap-4">
                                <label
                                    for="image"
                                    class="flex h-32 w-full cursor-pointer items-center justify-center rounded-lg border-2 border-dashed border-muted-foreground/25 transition-colors hover:border-muted-foreground/50 hover:bg-muted/50"
                                    :class="{ 'hidden': imagePreview }"
                                >
                                    <div class="text-center">
                                        <ImagePlus class="mx-auto mb-2 size-8 text-muted-foreground/50" />
                                        <p class="text-sm text-muted-foreground">Klik untuk upload gambar</p>
                                    </div>
                                    <input
                                        id="image"
                                        type="file"
                                        accept="image/*"
                                        class="hidden"
                                        @change="handleImageChange"
                                    />
                                </label>
                                <div v-if="imagePreview" class="relative w-full">
                                    <img :src="imagePreview" alt="Preview" class="h-48 w-full rounded-lg object-cover" />
                                    <Button
                                        type="button"
                                        size="icon"
                                        variant="destructive"
                                        class="absolute right-2 top-2 size-7"
                                        @click="removeImage"
                                    >
                                        <X class="size-3.5" />
                                    </Button>
                                </div>
                            </div>
                            <InputError :message="form.errors.image" />
                        </div>
                    </CardContent>

                    <CardFooter class="flex justify-end gap-2">
                        <Button variant="outline" as-child>
                            <Link href="/suggestions">Batal</Link>
                        </Button>
                        <Button type="submit" :disabled="form.processing">Kirim Saran</Button>
                    </CardFooter>
                </form>
            </Card>
        </div>
    </AppLayout>
</template>
