<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { usePwaInstall } from '@/composables/usePwaInstall';
import { Download, LogOut, ArrowRight, GraduationCap } from '@lucide/vue';

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
    router.post('/users/logout');
};
</script>

<template>
    <div class="min-h-screen bg-neutral-50 dark:bg-neutral-950 text-neutral-900 dark:text-neutral-100 flex flex-col">
        <Head :title="`${title || 'Student & Parent Portal'} - ${tenant.short_name || tenant.name}`" />

        <!-- Header -->
        <header class="border-b border-indigo-100 dark:border-indigo-950 bg-white/90 dark:bg-neutral-900/90 backdrop-blur sticky top-0 z-40">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-600 font-bold text-white shadow-sm shadow-indigo-500/20">
                        <GraduationCap class="size-5" />
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-base font-semibold tracking-tight text-neutral-900 dark:text-white">
                                {{ tenant.short_name || tenant.name }}
                            </h1>
                            <Badge variant="outline" class="border-indigo-500/40 text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/40 text-[10px] font-semibold uppercase tracking-wider">
                                Student & Parent Portal
                            </Badge>
                        </div>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">
                            {{ tenant.domain || 'users.portal' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Install PWA on demand -->
                    <Button
                        v-if="isInstallable"
                        variant="default"
                        size="sm"
                        @click="promptInstall"
                        class="bg-indigo-600 text-white hover:bg-indigo-500 font-medium text-xs flex items-center gap-1.5 shadow-sm"
                    >
                        <Download class="size-3.5" />
                        Install {{ tenant.short_name || tenant.name }} Portal
                    </Button>

                    <!-- Cross-portal link (standard <a> tag per PRD rule 6.2) -->
                    <a
                        href="/staff/dashboard"
                        class="hidden md:inline-flex items-center gap-1 text-xs text-neutral-500 hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-white transition-colors py-1.5 px-2.5 rounded-md hover:bg-neutral-100 dark:hover:bg-neutral-800"
                    >
                        Switch to Staff Portal
                        <ArrowRight class="size-3" />
                    </a>

                    <div v-if="user?.name" class="hidden sm:flex flex-col text-right">
                        <span class="text-xs font-medium text-neutral-900 dark:text-white">{{ user.name }}</span>
                        <span class="text-[10px] text-neutral-500 dark:text-neutral-400">{{ user.role || 'Student/Parent' }}</span>
                    </div>

                    <Button
                        variant="ghost"
                        size="sm"
                        @click="logout"
                        class="text-neutral-600 dark:text-neutral-300 hover:text-neutral-900 dark:hover:text-white text-xs flex items-center gap-1"
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
