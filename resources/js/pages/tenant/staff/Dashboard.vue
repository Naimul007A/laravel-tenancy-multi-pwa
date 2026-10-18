<script setup lang="ts">
import StaffLayout from '@/layouts/StaffLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Users, BookOpen, Calendar, Clock, ArrowRight } from '@lucide/vue';

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

defineProps<{
    tenant: TenantProps;
    user: UserProps;
}>();
</script>

<template>
    <StaffLayout :tenant="tenant" :user="user" title="Staff Dashboard">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 space-y-6">
            <!-- Welcome Header -->
            <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <Badge variant="outline" class="border-amber-400/40 text-amber-400 bg-amber-400/10 text-xs">
                            PWA Scope: /staff/
                        </Badge>
                        <Badge variant="outline" class="border-slate-700 text-slate-300 text-xs">
                            Guard: staff
                        </Badge>
                    </div>
                    <h2 class="mt-2 text-2xl font-bold tracking-tight text-white">
                        Welcome back, {{ user.name || 'Teacher' }}!
                    </h2>
                    <p class="mt-1 text-sm text-slate-400">
                        {{ tenant.name }} Staff & Teacher Workspace
                    </p>
                </div>
                <!-- Dual role helper -->
                <div class="flex items-center gap-2">
                    <a
                        href="/users/dashboard"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-medium rounded-lg bg-indigo-600/20 text-indigo-300 border border-indigo-500/30 hover:bg-indigo-600/30 transition-colors"
                    >
                        Switch to Parent/Student Portal
                        <ArrowRight class="size-3.5" />
                    </a>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <Card class="border-slate-800 bg-slate-900 text-slate-100">
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-xs font-medium text-slate-400">Enrolled Students</CardTitle>
                        <Users class="size-4 text-amber-400" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-white">128</div>
                        <p class="text-xs text-slate-400 mt-1">Across 4 active courses</p>
                    </CardContent>
                </Card>

                <Card class="border-slate-800 bg-slate-900 text-slate-100">
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-xs font-medium text-slate-400">Today's Classes</CardTitle>
                        <Calendar class="size-4 text-amber-400" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-white">3</div>
                        <p class="text-xs text-slate-400 mt-1">Next: Physics at 10:30 AM</p>
                    </CardContent>
                </Card>

                <Card class="border-slate-800 bg-slate-900 text-slate-100">
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-xs font-medium text-slate-400">Assignments to Grade</CardTitle>
                        <BookOpen class="size-4 text-amber-400" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-white">14</div>
                        <p class="text-xs text-slate-400 mt-1">Due by Friday</p>
                    </CardContent>
                </Card>

                <Card class="border-slate-800 bg-slate-900 text-slate-100">
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-xs font-medium text-slate-400">PWA Status</CardTitle>
                        <Clock class="size-4 text-emerald-400" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-emerald-400">Active</div>
                        <p class="text-xs text-slate-400 mt-1">Service Worker scoped to /staff/</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Details Card -->
            <Card class="border-slate-800 bg-slate-900 text-slate-100">
                <CardHeader>
                    <CardTitle class="text-base text-white">Multi-Tenant Dual-PWA Configuration</CardTitle>
                    <CardDescription class="text-slate-400">
                        Current tenant and session diagnostics
                    </CardDescription>
                </CardHeader>
                <CardContent class="space-y-3 text-sm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 rounded-lg bg-slate-800/60 border border-slate-700/60">
                            <span class="text-xs text-slate-400 font-medium uppercase tracking-wider block mb-1">Active Tenant</span>
                            <p class="font-semibold text-white">{{ tenant.name }} ({{ tenant.id }})</p>
                            <p class="text-xs text-slate-400 mt-1 font-mono">{{ tenant.domain }}</p>
                        </div>
                        <div class="p-4 rounded-lg bg-slate-800/60 border border-slate-700/60">
                            <span class="text-xs text-slate-400 font-medium uppercase tracking-wider block mb-1">Authenticated Staff User</span>
                            <p class="font-semibold text-white">{{ user.name }}</p>
                            <p class="text-xs text-slate-400 mt-1 font-mono">{{ user.email }} (Role: {{ user.role }})</p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </StaffLayout>
</template>
