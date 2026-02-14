<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/AuthLayout.vue';

defineProps<{
    status?: string;
}>();

const verifyForm = useForm({
    code: '',
});

const resendForm = useForm({});

function submitVerify() {
    verifyForm.post('/email/verify-code', {
        preserveScroll: true,
    });
}

function submitResend() {
    resendForm.post('/email/resend-code', {
        preserveScroll: true,
    });
}
</script>

<template>
    <AuthLayout
        title="Verifikasi email"
        description="Masukkan kode 6 digit yang telah dikirim ke email Anda"
    >
        <Head title="Verifikasi Email" />

        <div
            v-if="status === 'verification-code-sent'"
            class="mb-4 text-center text-sm font-medium text-green-600"
        >
            Kode verifikasi baru telah dikirim ke alamat email Anda.
        </div>

        <form @submit.prevent="submitVerify" class="flex flex-col gap-6">
            <div class="grid gap-4">
                <div class="grid gap-2">
                    <Label for="code">Kode Verifikasi</Label>
                    <Input
                        id="code"
                        v-model="verifyForm.code"
                        type="text"
                        inputmode="numeric"
                        maxlength="6"
                        required
                        autofocus
                        autocomplete="one-time-code"
                        placeholder="000000"
                        class="text-center text-lg tracking-[0.5em]"
                    />
                    <InputError :message="verifyForm.errors.code" />
                </div>

                <Button
                    type="submit"
                    class="w-full"
                    :disabled="verifyForm.processing"
                >
                    <Spinner v-if="verifyForm.processing" />
                    Verifikasi
                </Button>
            </div>
        </form>

        <div class="mt-4 space-y-4 text-center">
            <p class="text-sm text-muted-foreground">Tidak menerima kode?</p>

            <form @submit.prevent="submitResend">
                <Button
                    type="submit"
                    variant="secondary"
                    :disabled="resendForm.processing"
                    class="w-full"
                >
                    <Spinner v-if="resendForm.processing" />
                    Kirim ulang kode
                </Button>
            </form>

            <TextLink href="/logout" as="button" class="mx-auto block text-sm">
                Log out
            </TextLink>
        </div>
    </AuthLayout>
</template>
