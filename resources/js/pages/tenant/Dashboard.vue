<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';

interface TenantProps {
    id: string;
    name: string;
    domain: string;
}

interface UserProps {
    id?: number;
    name?: string;
    email?: string;
}

defineProps<{
    tenant: TenantProps;
    user: UserProps;
}>();

const logout = () => {
    router.post('/logout');
};
</script>

<template>
    <div class="min-h-screen bg-background text-foreground">
        <Head :title="`Dashboard - ${tenant.name}`" />

        <!-- Navigation bar -->
        <header class="border-b border-border bg-card">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary font-bold text-primary-foreground shadow-sm">
                        {{ tenant.name.substring(0, 2).toUpperCase() }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-lg font-semibold tracking-tight text-card-foreground">
                                {{ tenant.name }}
                            </h1>
                            <Badge variant="outline" class="text-xs">
                                {{ tenant.id }}
                            </Badge>
                        </div>
                        <p class="text-xs text-muted-foreground">
                            {{ tenant.domain }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <span class="text-sm text-muted-foreground hidden sm:inline">
                        Signed in as <strong class="text-foreground">{{ user.name }}</strong>
                    </span>
                    <Button variant="outline" size="sm" @click="logout">
                        Log out
                    </Button>
                </div>
            </div>
        </header>

        <!-- Main content -->
        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h2 class="text-2xl font-bold tracking-tight">
                    Welcome to {{ tenant.name }}
                </h2>
                <p class="mt-1 text-sm text-muted-foreground">
                    This is your single-database multi-tenant dashboard running on the subdomain <code>{{ tenant.domain }}</code>.
                </p>
            </div>

            <!-- Dashboard cards -->
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>Tenant ID</CardDescription>
                        <CardTitle class="text-xl font-mono">{{ tenant.id }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-xs text-muted-foreground">
                            Unique identifier used for single-database tenant query scoping.
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>Active Domain</CardDescription>
                        <CardTitle class="text-xl truncate font-mono text-primary">{{ tenant.domain }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-xs text-muted-foreground">
                            Identified via <code>InitializeTenancyByDomain</code> middleware.
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>Authenticated User</CardDescription>
                        <CardTitle class="text-xl">{{ user.name }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-xs text-muted-foreground truncate">
                            {{ user.email }} (Tenant scoped)
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Architecture info banner -->
            <div class="mt-8 rounded-xl border border-primary/20 bg-primary/5 p-6 dark:bg-primary/10">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="text-base font-semibold text-primary">
                            Single-Database Architecture Active
                        </h3>
                        <p class="mt-1 text-sm text-muted-foreground">
                            All queries on this subdomain are automatically scoped with <code>tenant_id = '{{ tenant.id }}'</code>. Models using <code>BelongsToTenant</code> isolate data while sharing a single centralized database.
                        </p>
                    </div>
                    <Badge variant="secondary" class="self-start sm:self-center font-mono">
                        tenant_id: {{ tenant.id }}
                    </Badge>
                </div>
            </div>
        </main>
    </div>
</template>
