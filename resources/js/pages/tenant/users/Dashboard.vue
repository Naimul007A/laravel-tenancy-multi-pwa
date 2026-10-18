<script setup lang="ts">
import UserLayout from '@/layouts/UserLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { BookOpen, GraduationCap, Calendar, Award, ArrowRight } from '@lucide/vue';

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
    <UserLayout :tenant="tenant" :user="user" title="Student & Parent Dashboard">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 space-y-6">
            <!-- Welcome Header -->
            <div class="rounded-xl border border-indigo-100 dark:border-indigo-950 bg-gradient-to-r from-indigo-50/60 to-purple-50/40 dark:from-indigo-950/20 dark:to-purple-950/10 p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <Badge variant="outline" class="border-indigo-500/40 text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/40 text-xs">
                            PWA Scope: /users/
                        </Badge>
                        <Badge variant="outline" class="border-neutral-300 dark:border-neutral-700 text-xs">
                            Guard: web
                        </Badge>
                    </div>
                    <h2 class="mt-2 text-2xl font-bold tracking-tight text-neutral-900 dark:text-white">
                        Welcome, {{ user.name || 'Student / Parent' }}!
                    </h2>
                    <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">
                        {{ tenant.name }} Student & Parent Learning Portal
                    </p>
                </div>
                <!-- Dual role helper -->
                <div class="flex items-center gap-2">
                    <a
                        href="/staff/dashboard"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-medium rounded-lg bg-slate-900 text-amber-300 border border-slate-800 hover:bg-slate-800 transition-colors"
                    >
                        Switch to Staff Portal
                        <ArrowRight class="size-3.5" />
                    </a>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-xs font-medium text-neutral-500">Enrolled Courses</CardTitle>
                        <BookOpen class="size-4 text-indigo-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">5</div>
                        <p class="text-xs text-neutral-500 mt-1">Semester 1 active</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-xs font-medium text-neutral-500">Current GPA</CardTitle>
                        <Award class="size-4 text-indigo-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-indigo-600">3.85</div>
                        <p class="text-xs text-neutral-500 mt-1">Dean's Honor List</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-xs font-medium text-neutral-500">Attendance Rate</CardTitle>
                        <Calendar class="size-4 text-emerald-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-emerald-600">98.2%</div>
                        <p class="text-xs text-neutral-500 mt-1">Present 54/55 days</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-xs font-medium text-neutral-500">PWA Status</CardTitle>
                        <GraduationCap class="size-4 text-indigo-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-indigo-600">Installed</div>
                        <p class="text-xs text-neutral-500 mt-1">Service Worker scoped to /users/</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Details Card -->
            <Card>
                <CardHeader>
                    <CardTitle class="text-base">Portal Diagnostics & Account Information</CardTitle>
                    <CardDescription>
                        Isolated session parameters for the Student & Parent PWA
                    </CardDescription>
                </CardHeader>
                <CardContent class="space-y-3 text-sm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 rounded-lg bg-neutral-50 dark:bg-neutral-800/60 border border-neutral-200 dark:border-neutral-700/60">
                            <span class="text-xs text-neutral-500 font-medium uppercase tracking-wider block mb-1">Active Organization</span>
                            <p class="font-semibold">{{ tenant.name }} ({{ tenant.id }})</p>
                            <p class="text-xs text-neutral-500 mt-1 font-mono">{{ tenant.domain }}</p>
                        </div>
                        <div class="p-4 rounded-lg bg-neutral-50 dark:bg-neutral-800/60 border border-neutral-200 dark:border-neutral-700/60">
                            <span class="text-xs text-neutral-500 font-medium uppercase tracking-wider block mb-1">Authenticated User</span>
                            <p class="font-semibold">{{ user.name }}</p>
                            <p class="text-xs text-neutral-500 mt-1 font-mono">{{ user.email }} (Role: {{ user.role }})</p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </UserLayout>
</template>
