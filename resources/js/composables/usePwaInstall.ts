import { ref, onMounted, onUnmounted } from 'vue';

interface BeforeInstallPromptEvent extends Event {
    prompt: () => Promise<void>;
    userChoice: Promise<{ outcome: 'accepted' | 'dismissed'; platform: string }>;
}

const deferredPrompt = ref<BeforeInstallPromptEvent | null>(null);
const isInstalled = ref(false);
const isInstallable = ref(false);

export function usePwaInstall() {
    const handleBeforeInstallPrompt = (e: Event) => {
        e.preventDefault();
        deferredPrompt.value = e as BeforeInstallPromptEvent;
        isInstallable.value = true;
    };

    const handleAppInstalled = () => {
        isInstalled.value = true;
        isInstallable.value = false;
        deferredPrompt.value = null;
    };

    onMounted(() => {
        if (typeof window === 'undefined') return;

        // Check if already in standalone display mode
        if (
            window.matchMedia('(display-mode: standalone)').matches ||
            (window.navigator as unknown as { standalone?: boolean }).standalone === true
        ) {
            isInstalled.value = true;
        }

        window.addEventListener('beforeinstallprompt', handleBeforeInstallPrompt);
        window.addEventListener('appinstalled', handleAppInstalled);
    });

    onUnmounted(() => {
        if (typeof window === 'undefined') return;
        window.removeEventListener('beforeinstallprompt', handleBeforeInstallPrompt);
        window.removeEventListener('appinstalled', handleAppInstalled);
    });

    const promptInstall = async (): Promise<boolean> => {
        if (!deferredPrompt.value) {
            return false;
        }

        try {
            await deferredPrompt.value.prompt();
            const choiceResult = await deferredPrompt.value.userChoice;
            deferredPrompt.value = null;
            isInstallable.value = false;
            return choiceResult.outcome === 'accepted';
        } catch {
            return false;
        }
    };

    return {
        deferredPrompt,
        isInstalled,
        isInstallable,
        promptInstall,
    };
}
