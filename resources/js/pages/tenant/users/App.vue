<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { usePwaInstall } from '@/composables/usePwaInstall';
import { Download, GraduationCap, Smartphone, Monitor, CheckCircle2, ArrowRight, Share2 } from '@lucide/vue';
import { ref } from 'vue';

interface TenantProps {
    id: string;
    name: string;
    short_name?: string;
    domain: string;
    theme_color?: string;
    icon_192?: string;
    icon_512?: string;
}

const props = defineProps<{
    tenant: TenantProps;
    features: Record<string, string>;
}>();

const { isInstallable, isInstalled, promptInstall } = usePwaInstall();
const isInstalling = ref(false);

const handleInstall = async () => {
    isInstalling.value = true;
    try {
        await promptInstall();
    } finally {
        isInstalling.value = false;
    }
};

const continueInBrowser = () => {
    // Set a bypass cookie for 24 hours so user can access web if needed
    document.cookie = "pwa_bypass=1; path=/; max-age=86400; SameSite=Lax";
    router.visit('/users/login');
};
</script>

<template>
    <div class="min-h-screen bg-neutral-50 dark:bg-neutral-950 text-neutral-900 dark:text-neutral-100 flex flex-col items-center justify-center p-4 sm:p-6 lg:p-8">
        <Head :title="`Install Student & Parent App - ${tenant.short_name || tenant.name}`" />

        <div class="w-full max-w-2xl space-y-8 my-auto">
            <!-- App Hero Card -->
            <div class="rounded-3xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 p-8 sm:p-10 shadow-2xl backdrop-blur text-center space-y-6">
                <!-- App Icon -->
                <div class="flex justify-center">
                    <div class="relative">
                        <div class="h-24 w-24 rounded-2xl bg-indigo-600 flex items-center justify-center shadow-xl shadow-indigo-500/20 overflow-hidden">
                            <img
                                v-if="tenant.icon_192"
                                :src="tenant.icon_192"
                                :alt="tenant.name"
                                class="h-full w-full object-cover"
                            />
                            <GraduationCap v-else class="size-12 text-white" />
                        </div>
                        <div class="absolute -bottom-2 -right-2 rounded-full bg-indigo-500 p-1 text-white shadow">
                            <GraduationCap class="size-4" />
                        </div>
                    </div>
                </div>

                <!-- Titles -->
                <div class="space-y-2">
                    <div class="flex justify-center">
                        <Badge variant="outline" class="border-indigo-500/40 text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/40 text-xs font-semibold uppercase tracking-wider">
                            Student & Parent Learning App
                        </Badge>
                    </div>
                    <h1 class="text-3xl font-extrabold tracking-tight text-neutral-900 dark:text-white sm:text-4xl">
                        {{ tenant.short_name || tenant.name }} Portal App
                    </h1>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 max-w-md mx-auto">
                        Install the official student & parent app on your device for real-time grade updates, attendance tracking, and instant alerts.
                    </p>
                </div>

                <!-- Primary Action -->
                <div class="pt-2 max-w-sm mx-auto space-y-3">
                    <div v-if="isInstalled" class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-4 text-center space-y-2">
                        <div class="flex items-center justify-center gap-2 text-emerald-600 dark:text-emerald-400 font-semibold text-sm">
                            <CheckCircle2 class="size-5" />
                            App Already Installed
                        </div>
                        <p class="text-xs text-neutral-600 dark:text-neutral-300">
                            You can open the portal app directly from your home screen or app menu.
                        </p>
                        <Button
                            @click="continueInBrowser"
                            class="w-full bg-emerald-600 text-white hover:bg-emerald-500 font-semibold text-sm"
                        >
                            Open Student & Parent Portal
                        </Button>
                    </div>

                    <div v-else class="space-y-3">
                        <Button
                            size="lg"
                            @click="handleInstall"
                            :disabled="isInstalling"
                            class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-base py-6 shadow-lg shadow-indigo-600/25 flex items-center justify-center gap-2 cursor-pointer transition-all hover:scale-[1.02]"
                        >
                            <Download class="size-5" />
                            <span>{{ isInstalling ? 'Installing App...' : `Install ${tenant.short_name || tenant.name} App` }}</span>
                        </Button>

                        <p v-if="!isInstallable" class="text-xs text-indigo-600 dark:text-indigo-400">
                            Tap browser menu or Share to add this app to your Home Screen
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="continueInBrowser"
                        class="text-xs text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white transition-colors underline underline-offset-4 block mx-auto pt-2"
                    >
                        Continue in web browser instead &rarr;
                    </button>
                </div>
            </div>

            <!-- Features Grid -->
            <div class="grid gap-4 sm:grid-cols-2">
                <Card
                    v-for="(desc, title) in features"
                    :key="title"
                    class="border-neutral-200 dark:border-neutral-800 bg-white/70 dark:bg-neutral-900/60"
                >
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-semibold flex items-center gap-2">
                            <CheckCircle2 class="size-4 text-indigo-600 dark:text-indigo-400 shrink-0" />
                            {{ title }}
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 leading-relaxed">
                            {{ desc }}
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Installation Instructions Guide -->
            <Card class="border-neutral-200 dark:border-neutral-800">
                <CardHeader>
                    <CardTitle class="text-sm flex items-center gap-2">
                        <Smartphone class="size-4 text-indigo-600 dark:text-indigo-400" />
                        How to Install on Your Device
                    </CardTitle>
                    <CardDescription class="text-xs">
                        Quick guide for iOS, Android, and Desktop
                    </CardDescription>
                </CardHeader>
                <CardContent class="grid gap-4 sm:grid-cols-3 text-xs">
                    <div class="p-3 rounded-xl bg-neutral-100 dark:bg-neutral-800/40 border border-neutral-200 dark:border-neutral-700/50 space-y-1.5">
                        <div class="font-semibold flex items-center gap-1.5">
                            <Share2 class="size-3.5 text-indigo-600 dark:text-indigo-400" />
                            iOS / iPhone / iPad
                        </div>
                        <p class="text-neutral-600 dark:text-neutral-400">
                            1. In Safari, tap the <strong>Share</strong> button at bottom.
                        </p>
                        <p class="text-neutral-600 dark:text-neutral-400">
                            2. Scroll down and tap <strong>Add to Home Screen</strong>.
                        </p>
                    </div>

                    <div class="p-3 rounded-xl bg-neutral-100 dark:bg-neutral-800/40 border border-neutral-200 dark:border-neutral-700/50 space-y-1.5">
                        <div class="font-semibold flex items-center gap-1.5">
                            <Download class="size-3.5 text-indigo-600 dark:text-indigo-400" />
                            Android (Chrome)
                        </div>
                        <p class="text-neutral-600 dark:text-neutral-400">
                            1. Tap the <strong>Install</strong> button above or 3-dots menu.
                        </p>
                        <p class="text-neutral-600 dark:text-neutral-400">
                            2. Tap <strong>Install app</strong> or <strong>Add to Home screen</strong>.
                        </p>
                    </div>

                    <div class="p-3 rounded-xl bg-neutral-100 dark:bg-neutral-800/40 border border-neutral-200 dark:border-neutral-700/50 space-y-1.5">
                        <div class="font-semibold flex items-center gap-1.5">
                            <Monitor class="size-3.5 text-indigo-600 dark:text-indigo-400" />
                            Desktop (Mac / PC)
                        </div>
                        <p class="text-neutral-600 dark:text-neutral-400">
                            1. Click the <strong>Install</strong> icon in the browser address bar.
                        </p>
                        <p class="text-neutral-600 dark:text-neutral-400">
                            2. Or click the <strong>Install</strong> button above to add to your dock.
                        </p>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
