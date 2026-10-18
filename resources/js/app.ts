import { createInertiaApp } from '@inertiajs/vue3';
import { initializeTheme } from '@/composables/useAppearance';
import AppLayout from '@/layouts/AppLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { initializeFlashToast } from '@/lib/flashToast';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

void createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    layout: (name) => {
        switch (true) {
            case name === 'Welcome':
            case name.startsWith('tenant/'):
                return null;
            case name.startsWith('auth/'):
                return AuthLayout;
            case name.startsWith('settings/'):
                return [AppLayout, SettingsLayout];
            default:
                return AppLayout;
        }
    },
    withApp: (app) => {
        app.directive('focus', {
            mounted: (el: HTMLElement, shouldFocus) => {
                if (shouldFocus.value !== false) {
                    el.focus();
                }
            },
        });
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();

// This will listen for flash toast data from the server...
initializeFlashToast();

// Multi-Tenant Dual-PWA Scoped Service Worker Registration
if (typeof window !== 'undefined' && 'serviceWorker' in navigator) {
    const isStaff = window.location.pathname.startsWith('/staff');
    const isUsers = window.location.pathname.startsWith('/users');

    if (isStaff || isUsers) {
        window.addEventListener('load', () => {
            navigator.serviceWorker
                .register('/sw.js', { scope: isStaff ? '/staff/' : '/users/' })
                .catch((error) => {
                    console.error('PWA Service Worker registration failed:', error);
                });
        });
    }
}
