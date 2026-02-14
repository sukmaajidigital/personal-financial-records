<script setup lang="ts">
import { onMounted, onUnmounted } from 'vue';
import { toast } from 'vue-sonner';
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
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
</template>
