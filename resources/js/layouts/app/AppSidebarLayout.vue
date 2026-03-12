<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import {
    BarChart3,
    ClipboardList,
    MessageSquarePlus,
    Sparkles,
    TrendingUp,
    Check,
} from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { toast } from 'vue-sonner';
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Toaster } from '@/components/ui/sonner';
import type { BreadcrumbItem } from '@/types';

type Props = {
    breadcrumbs?: BreadcrumbItem[];
};

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

function onInertiaSuccess(event: Event) {
    const detail = (event as CustomEvent).detail;
    const flash = detail?.page?.props?.flash as
        | { success?: string }
        | undefined;
    if (flash?.success) {
        toast.success(flash.success, { duration: 2000 });
    }
}

onMounted(() => {
    document.addEventListener('inertia:success', onInertiaSuccess);
});

onUnmounted(() => {
    document.removeEventListener('inertia:success', onInertiaSuccess);
});

// --- Feature Notification ---
type FeatureItem = {
    icon: string;
    title: string;
    description: string;
    details?: string[];
};

type FeatureNotification = {
    version: string;
    title: string;
    features: FeatureItem[];
} | null;

const page = usePage();
const notification = computed(
    () => page.props.featureNotification as FeatureNotification,
);
const showNotification = ref(false);
const dismissing = ref(false);

// Show notification on mount if available
watch(
    notification,
    (val) => {
        if (val) showNotification.value = true;
    },
    { immediate: true },
);

const iconComponents: Record<string, any> = {
    'clipboard-list': ClipboardList,
    'message-square-plus': MessageSquarePlus,
    'bar-chart-3': BarChart3,
    'trending-up': TrendingUp,
};

function getIcon(name: string) {
    return iconComponents[name] || Sparkles;
}

function dismissNotification() {
    dismissing.value = true;
    router.post(
        '/dismiss-notification',
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                showNotification.value = false;
                dismissing.value = false;
            },
            onError: () => {
                dismissing.value = false;
            },
        },
    );
}
</script>

<template>
    <Toaster position="top-center" :duration="2000" />
    <AppShell variant="sidebar">
        <AppSidebar />
        <AppContent variant="sidebar" class="overflow-x-hidden">
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />
            <slot />
        </AppContent>
    </AppShell>

    <!-- Feature Update Notification Modal -->
    <Dialog v-model:open="showNotification">
        <DialogContent class="max-h-[85vh] overflow-y-auto sm:max-w-lg">
            <DialogHeader>
                <DialogTitle class="flex items-center gap-2 text-lg">
                    <Sparkles class="size-5 text-amber-500" />
                    {{ notification?.title }}
                </DialogTitle>
                <DialogDescription>
                    Berikut adalah fitur-fitur baru yang telah ditambahkan ke
                    aplikasi.
                </DialogDescription>
            </DialogHeader>

            <div class="space-y-4 py-2">
                <div
                    v-for="(feature, idx) in notification?.features"
                    :key="idx"
                    class="rounded-lg border p-4 transition-colors hover:bg-muted/50"
                >
                    <div class="flex items-start gap-3">
                        <div
                            class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary"
                        >
                            <component
                                :is="getIcon(feature.icon)"
                                class="size-4.5"
                            />
                        </div>
                        <div class="min-w-0 flex-1">
                            <h4 class="text-sm font-semibold">
                                {{ feature.title }}
                            </h4>
                            <p class="mt-0.5 text-xs text-muted-foreground">
                                {{ feature.description }}
                            </p>
                            <ul
                                v-if="feature.details?.length"
                                class="mt-2 space-y-1"
                            >
                                <li
                                    v-for="(detail, dIdx) in feature.details"
                                    :key="dIdx"
                                    class="flex items-start gap-2 text-xs text-muted-foreground"
                                >
                                    <Check
                                        class="mt-0.5 size-3 shrink-0 text-green-500"
                                    />
                                    <span>{{ detail }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <DialogFooter>
                <Button
                    @click="dismissNotification"
                    :disabled="dismissing"
                    class="w-full"
                >
                    <Check class="mr-1.5 size-4" />
                    Mengerti, Terima Kasih!
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
