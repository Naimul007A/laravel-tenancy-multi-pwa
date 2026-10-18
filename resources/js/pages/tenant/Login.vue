<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

interface TenantProps {
    id: string;
    name: string;
    domain: string;
}

defineProps<{
    tenant: TenantProps;
    status?: string;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <div class="flex min-h-screen flex-col items-center justify-center bg-background px-4 py-12 sm:px-6 lg:px-8">
        <Head :title="`Login - ${tenant.name}`" />

        <div class="w-full max-w-md space-y-6 rounded-2xl border border-border bg-card p-8 shadow-sm">
            <div class="text-center">
                <div class="mb-3 flex justify-center">
                    <Badge variant="secondary" class="text-xs font-semibold uppercase tracking-wider">
                        Tenant Workspace
                    </Badge>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-card-foreground">
                    {{ tenant.name }}
                </h1>
                <p class="mt-1 text-xs text-muted-foreground">
                    {{ tenant.domain }}
                </p>
                <p class="mt-4 text-sm text-muted-foreground">
                    Enter your workspace credentials to sign in
                </p>
            </div>

            <div
                v-if="status"
                class="rounded-lg bg-green-50 p-3 text-center text-sm font-medium text-green-700 dark:bg-green-950 dark:text-green-300"
            >
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input
                        id="email"
                        v-model="form.email"
                        type="email"
                        required
                        autofocus
                        autocomplete="email"
                        placeholder="you@example.com"
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <PasswordInput
                        id="password"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        placeholder="Password"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="flex items-center">
                    <Label for="remember" class="flex items-center space-x-2 text-sm font-normal">
                        <Checkbox
                            id="remember"
                            :checked="form.remember"
                            @update:checked="(val: boolean | 'indeterminate') => (form.remember = val === true)"
                        />
                        <span>Remember me</span>
                    </Label>
                </div>

                <Button type="submit" class="w-full" :disabled="form.processing">
                    <Spinner v-if="form.processing" class="mr-2" />
                    Sign in to workspace
                </Button>
            </form>
        </div>
    </div>
</template>
