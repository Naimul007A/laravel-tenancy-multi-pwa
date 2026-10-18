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
import { usePwaInstall } from '@/composables/usePwaInstall';
import { Download, GraduationCap, ArrowRight } from '@lucide/vue';

interface TenantProps {
    id: string;
    name: string;
    short_name?: string;
    domain: string;
    theme_color?: string;
}

const props = defineProps<{
    tenant: TenantProps;
    status?: string;
    initialEmail?: string;
}>();

const { isInstallable, promptInstall } = usePwaInstall();

const form = useForm({
    email: props.initialEmail ?? '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/users/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <div class="flex min-h-screen flex-col items-center justify-center bg-neutral-50 dark:bg-neutral-950 px-4 py-12 sm:px-6 lg:px-8 text-neutral-900 dark:text-neutral-100">
        <Head :title="`Portal Login - ${tenant.short_name || tenant.name}`" />

        <div class="w-full max-w-md space-y-6 rounded-2xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 p-8 shadow-xl">
            <div class="text-center">
                <div class="mb-3 flex justify-center">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-md shadow-indigo-500/20">
                        <GraduationCap class="size-6" />
                    </div>
                </div>
                <div class="flex justify-center mb-2">
                    <Badge variant="outline" class="border-indigo-500/40 text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/40 text-xs font-semibold uppercase tracking-wider">
                        Student & Parent Portal
                    </Badge>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-neutral-900 dark:text-white">
                    {{ tenant.short_name || tenant.name }}
                </h1>
                <p class="mt-1 text-xs text-neutral-500 font-mono">
                    {{ tenant.domain }}
                </p>
                <p class="mt-4 text-sm text-neutral-600 dark:text-neutral-300">
                    Sign in to access your student or parent account
                </p>
            </div>

            <div
                v-if="status"
                class="rounded-lg bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 p-3 text-center text-sm font-medium text-emerald-800 dark:text-emerald-300"
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
                        autocomplete="username"
                        placeholder="parent@example.com"
                        class="focus-visible:ring-indigo-500"
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
                        class="focus-visible:ring-indigo-500"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <Checkbox
                            id="remember"
                            v-model="form.remember"
                        />
                        <Label for="remember" class="text-sm font-normal cursor-pointer">
                            Remember me
                        </Label>
                    </div>
                </div>

                <Button
                    type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-semibold shadow-md transition"
                    :disabled="form.processing"
                >
                    <Spinner v-if="form.processing" class="mr-2" />
                    Sign in to Student/Parent Portal
                </Button>
            </form>

            <div v-if="isInstallable" class="pt-2 border-t border-neutral-100 dark:border-neutral-800">
                <Button
                    variant="outline"
                    size="sm"
                    @click="promptInstall"
                    class="w-full flex items-center justify-center gap-1.5"
                >
                    <Download class="size-3.5" />
                    Install {{ tenant.short_name || tenant.name }} Portal App
                </Button>
            </div>

            <div class="pt-4 border-t border-neutral-100 dark:border-neutral-800 text-center">
                <a
                    href="/staff/login"
                    class="inline-flex items-center gap-1 text-xs text-neutral-500 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors"
                >
                    Are you a teacher or staff member? Go to Staff Portal
                    <ArrowRight class="size-3" />
                </a>
            </div>
        </div>
    </div>
</template>
