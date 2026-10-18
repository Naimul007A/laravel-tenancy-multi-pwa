<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { usePwaInstall } from '@/composables/usePwaInstall';
import { Download, Shield, Smartphone, Monitor, CheckCircle2, ArrowRight, Share2, PlusSquare } from '@lucide/vue';
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
    router.visit('/staff/login');
};
</script>

<template>
    <div class="min-h-screen bg-slate-950 text-slate-100 flex flex-col items-center justify-center p-4 sm:p-6 lg:p-8">
        <Head :title="`Install Staff App - ${tenant.short_name || tenant.name}`" />

        <div class="w-full max-w-2xl space-y-8 my-auto">
            <!-- App Hero Card -->
            <div class="rounded-3xl border border-slate-800 bg-slate-900/90 p-8 sm:p-10 shadow-2xl backdrop-blur text-center space-y-6">
                <!-- App Icon -->
                <div class="flex justify-center">
                    <div class="relative">
                        <div class="h-24 w-24 rounded-2xl bg-slate-800 border-2 border-slate-700 flex items-center justify-center shadow-xl shadow-amber-400/5 overflow-hidden">
                            <img
                                v-if="tenant.icon_192"
                                :src="tenant.icon_192"
                                :alt="tenant.name"
                                class="h-full w-full object-cover"
                            />
                            <Shield v-else class="size-12 text-amber-400" />
                        </div>
                        <div class="absolute -bottom-2 -right-2 rounded-full bg-amber-400 p-1 text-slate-950 shadow">
                            <Shield class="size-4" />
                        </div>
                    </div>
                </div>

                <!-- Titles -->
                <div class="space-y-2">
                    <div class="flex justify-center">
                        <Badge variant="outline" class="border-amber-400/40 text-amber-400 bg-amber-400/10 text-xs font-semibold uppercase tracking-wider">
                            Official Staff & Teachers Application
                        </Badge>
                    </div>
                    <h1 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                        {{ tenant.short_name || tenant.name }} Staff App
                    </h1>
                    <p class="text-sm text-slate-400 max-w-md mx-auto">
                        For security and uninterrupted access, this portal is optimized to run as a dedicated application on your mobile device or computer.
                    </p>
                </div>

                <!-- Primary Action -->
                <div class="pt-2 max-w-sm mx-auto space-y-3">
                    <div v-if="isInstalled" class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-4 text-center space-y-2">
                        <div class="flex items-center justify-center gap-2 text-emerald-400 font-semibold text-sm">
                            <CheckCircle2 class="size-5" />
                            App Already Installed
                        </div>
                        <p class="text-xs text-slate-300">
                            Launch the application directly from your device home screen or desktop dock.
                        </p>
                        <Button
                            @click="continueInBrowser"
                            class="w-full bg-emerald-500 text-slate-950 hover:bg-emerald-400 font-semibold text-sm"
                        >
                            Open Staff Workspace
                        </Button>
                    </div>

                    <div v-else class="space-y-3">
                        <Button
                            size="lg"
                            @click="handleInstall"
                            :disabled="isInstalling"
                            class="w-full bg-amber-400 text-slate-950 hover:bg-amber-300 font-bold text-base py-6 shadow-lg shadow-amber-400/20 flex items-center justify-center gap-2 cursor-pointer transition-all hover:scale-[1.02]"
                        >
                            <Download class="size-5" />
                            <span>{{ isInstalling ? 'Installing App...' : `Install ${tenant.short_name || tenant.name} Staff App` }}</span>
                        </Button>

                        <p v-if="!isInstallable" class="text-xs text-amber-300/80">
                            Tap browser menu or Share to add this app to your Home Screen
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="continueInBrowser"
                        class="text-xs text-slate-400 hover:text-white transition-colors underline underline-offset-4 block mx-auto pt-2"
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
                    class="border-slate-800 bg-slate-900/60 text-slate-200"
                >
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-semibold text-white flex items-center gap-2">
                            <CheckCircle2 class="size-4 text-amber-400 shrink-0" />
                            {{ title }}
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            {{ desc }}
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Installation Instructions Guide -->
            <Card class="border-slate-800 bg-slate-900/40 text-slate-300">
                <CardHeader>
                    <CardTitle class="text-sm text-white flex items-center gap-2">
                        <Smartphone class="size-4 text-amber-400" />
                        How to Install on Your Device
                    </CardTitle>
                    <CardDescription class="text-xs text-slate-400">
                        Quick guide for iOS, Android, and Desktop
                    </CardDescription>
                </CardHeader>
                <CardContent class="grid gap-4 sm:grid-cols-3 text-xs">
                    <div class="p-3 rounded-xl bg-slate-800/40 border border-slate-700/50 space-y-1.5">
                        <div class="font-semibold text-white flex items-center gap-1.5">
                            <Share2 class="size-3.5 text-amber-400" />
                            iOS / iPhone / iPad
                        </div>
                        <p class="text-slate-400">
                            1. In Safari, tap the <strong>Share</strong> button at bottom.
                        </p>
                        <p class="text-slate-400">
                            2. Scroll down and tap <strong>Add to Home Screen</strong>.
                        </p>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-800/40 border border-slate-700/50 space-y-1.5">
                        <div class="font-semibold text-white flex items-center gap-1.5">
                            <Download class="size-3.5 text-amber-400" />
                            Android (Chrome)
                        </div>
                        <p class="text-slate-400">
                            1. Tap the <strong>Install</strong> button above or 3-dots menu.
                        </p>
                        <p class="text-slate-400">
                            2. Tap <strong>Install app</strong> or <strong>Add to Home screen</strong>.
                        </p>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-800/40 border border-slate-700/50 space-y-1.5">
                        <div class="font-semibold text-white flex items-center gap-1.5">
                            <Monitor class="size-3.5 text-amber-400" />
                            Desktop (Mac / PC)
                        </div>
                        <p class="text-slate-400">
                            1. Click the <strong>Install</strong> icon in the browser address bar.
                        </p>
                        <p class="text-slate-400">
                            2. Or click the <strong>Install</strong> button above to add to your dock.
                        </p>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
