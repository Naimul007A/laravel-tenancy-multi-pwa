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
import { Download, Shield, ArrowRight } from '@lucide/vue';

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
    form.post('/staff/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <div class="flex min-h-screen flex-col items-center justify-center bg-slate-950 px-4 py-12 sm:px-6 lg:px-8 text-slate-100">
        <Head :title="`Staff Login - ${tenant.short_name || tenant.name}`" />

        <div class="w-full max-w-md space-y-6 rounded-2xl border border-slate-800 bg-slate-900 p-8 shadow-2xl">
            <div class="text-center">
                <div class="mb-3 flex justify-center">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-800 border border-slate-700 text-amber-400 shadow-md">
                        <Shield class="size-6" />
                    </div>
                </div>
                <div class="flex justify-center mb-2">
                    <Badge variant="outline" class="border-amber-400/40 text-amber-400 bg-amber-400/10 text-xs font-semibold uppercase tracking-wider">
                        Staff & Teachers PWA
                    </Badge>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-white">
                    {{ tenant.short_name || tenant.name }}
                </h1>
                <p class="mt-1 text-xs text-slate-400 font-mono">
                    {{ tenant.domain }}
                </p>
                <p class="mt-4 text-sm text-slate-300">
                    Sign in with your staff credentials
                </p>
            </div>

            <div
                v-if="status"
                class="rounded-lg bg-emerald-950/80 border border-emerald-800 p-3 text-center text-sm font-medium text-emerald-300"
            >
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <div class="grid gap-2">
                    <Label for="email" class="text-slate-200">Staff Email</Label>
                    <Input
                        id="email"
                        v-model="form.email"
                        type="email"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="teacher@school.edu"
                        class="bg-slate-800 border-slate-700 text-white placeholder:text-slate-500 focus-visible:ring-amber-400"
                    />
                    <InputError :message="form.errors.email" class="text-rose-400" />
                </div>

                <div class="grid gap-2">
                    <Label for="password" class="text-slate-200">Password</Label>
                    <PasswordInput
                        id="password"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        class="bg-slate-800 border-slate-700 text-white focus-visible:ring-amber-400"
                    />
                    <InputError :message="form.errors.password" class="text-rose-400" />
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <Checkbox
                            id="remember"
                            v-model="form.remember"
                            class="border-slate-700 data-[state=checked]:bg-amber-400 data-[state=checked]:text-slate-950"
                        />
                        <Label for="remember" class="text-sm font-normal text-slate-300 cursor-pointer">
                            Remember me
                        </Label>
                    </div>
                </div>

                <Button
                    type="submit"
                    class="w-full bg-amber-400 text-slate-950 hover:bg-amber-300 font-semibold shadow-md transition"
                    :disabled="form.processing"
                >
                    <Spinner v-if="form.processing" class="mr-2" />
                    Sign in to Staff Portal
                </Button>
            </form>

            <div v-if="isInstallable" class="pt-2 border-t border-slate-800">
                <Button
                    variant="outline"
                    size="sm"
                    @click="promptInstall"
                    class="w-full border-slate-700 text-slate-300 hover:text-white hover:bg-slate-800 flex items-center justify-center gap-1.5"
                >
                    <Download class="size-3.5" />
                    Install {{ tenant.short_name || tenant.name }} Staff PWA
                </Button>
            </div>

            <div class="pt-4 border-t border-slate-800/80 text-center">
                <a
                    href="/users/login"
                    class="inline-flex items-center gap-1 text-xs text-slate-400 hover:text-white transition-colors"
                >
                    Are you a student or parent? Go to Student/Parent Portal
                    <ArrowRight class="size-3" />
                </a>
            </div>
        </div>
    </div>
</template>
