<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    ArrowLeftRight,
    BarChart3,
    ClipboardList,
    GitBranch,
    LayoutGrid,
    MessageSquarePlus,
    Tag,
    Globe,
} from 'lucide-vue-next';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Transaksi',
        href: '/transactions',
        icon: ArrowLeftRight,
    },
    {
        title: 'Perencanaan',
        href: '/planned-transactions',
        icon: ClipboardList,
    },
    {
        title: 'Kategori',
        href: '/categories',
        icon: Tag,
    },
    {
        title: 'Analitik',
        href: '/analytics',
        icon: BarChart3,
    },
    {
        title: 'Saran',
        href: '/suggestions',
        icon: MessageSquarePlus,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'sukmaajidigital',
        href: 'https://github.com/sukmaajidigital',
        icon: GitBranch,
    },
    {
        title: 'Website',
        href: 'https://sukmaaji.my.id',
        icon: Globe,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
