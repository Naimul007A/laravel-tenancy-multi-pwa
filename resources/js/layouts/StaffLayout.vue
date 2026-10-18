<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { usePwaInstall } from '@/composables/usePwaInstall';
import { Download, LogOut, ArrowRight, Shield } from '@lucide/vue';

interface TenantProps {
    id: string;
    name: string;
    short_name?: string;
    domain?: string;
    theme_color?: string;
}

interface UserProps {
    id?: number;
    name?: string;
    email?: string;
    role?: string;
}

const props = defineProps<{
    tenant: TenantProps;
    user?: UserProps;
    title?: string;
}>();

const { isInstallable, promptInstall } = usePwaInstall();

const logout = () => {
    router.post('/staff/logout');
};
</script>

<template>
    <div class="min-h-screen bg-slate-950 text-slate-100 flex flex-col">
        <Head :title="`${title || 'Staff Portal'} - ${tenant.short_name || tenant.name}`" />

        <!-- Header -->
        <header class="border-b border-slate-800 bg-slate-900/90 backdrop-blur sticky top-0 z-40">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-800 border border-slate-700 font-bold text-amber-400 shadow-sm">
                        <Shield class="size-5" />
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-base font-semibold tracking-tight text-white">
                                {{ tenant.short_name || tenant.name }}
                            </h1>
                            <Badge variant="outline" class="border-amber-400/40 text-amber-400 bg-amber-400/10 text-[10px] font-semibold uppercase tracking-wider">
                                Staff & Teachers
                            </Badge>
                        </div>
                        <p class="text-xs text-slate-400">
                            {{ tenant.domain || 'staff.portal' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Install PWA on demand -->
                    <Button
                        v-if="isInstallable"
                        variant="secondary"
                        size="sm"
                        @click="promptInstall"
                        class="bg-amber-400 text-slate-950 hover:bg-amber-300 font-medium text-xs flex items-center gap-1.5"
                    >
                        <Download class="size-3.5" />
                        Install {{ tenant.short_name || tenant.name }} Staff
                    </Button>

                    <!-- Cross-portal link (standard <a> tag per PRD rule 6.2) -->
                    <a
                        href="/users/dashboard"
                        class="hidden md:inline-flex items-center gap-1 text-xs text-slate-400 hover:text-white transition-colors py-1.5 px-2.5 rounded-md hover:bg-slate-800"
                    >
                        Switch to Student/Parent Portal
                        <ArrowRight class="size-3" />
                    </a>

                    <div v-if="user?.name" class="hidden sm:flex flex-col text-right">
                        <span class="text-xs font-medium text-white">{{ user.name }}</span>
                        <span class="text-[10px] text-slate-400">{{ user.role || 'Staff' }}</span>
                    </div>

                    <Button
                        variant="ghost"
                        size="sm"
                        @click="logout"
                        class="text-slate-300 hover:text-white hover:bg-slate-800 text-xs flex items-center gap-1"
                    >
                        <LogOut class="size-3.5" />
                        Log out
                    </Button>
                </div>
            </div>
        </header>

        <!-- Main content -->
        <main class="flex-1">
            <slot />
        </main>
    </div>
</template>
