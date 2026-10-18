<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Inline script to detect system dark mode preference and apply it immediately --}}
        <script>
            (function() {
                const appearance = '{{ $appearance ?? "system" }}';

                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }
            })();

            // Force PWA Installation: redirect non-standalone web visitors to /{portal}/app
            (function() {
                try {
                    const isStandalone = window.matchMedia('(display-mode: standalone)').matches ||
                                         (window.navigator && window.navigator.standalone === true) ||
                                         new URLSearchParams(window.location.search).get('standalone') === '1' ||
                                         document.cookie.indexOf('pwa_bypass=1') !== -1 ||
                                         new URLSearchParams(window.location.search).get('bypass') === '1';

                    if (isStandalone) {
                        document.cookie = "pwa_standalone=1; path=/; max-age=31536000; SameSite=Lax";
                        return;
                    }

                    const path = window.location.pathname;
                    if (path.startsWith('/staff') && path !== '/staff/app') {
                        window.location.replace('/staff/app');
                    } else if (path.startsWith('/users') && path !== '/users/app') {
                        window.location.replace('/users/app');
                    }
                } catch (e) {
                    // Ignore detection errors in headless / non-standard environments
                }
            })();
        </script>

        {{-- Inline style to set the HTML background color based on our theme in app.css --}}
        <style>
            html {
                background-color: oklch(1 0 0);
            }

            html.dark {
                background-color: oklch(0.145 0 0);
            }
        </style>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        @fonts

        @php
            $portal = request()->is('staff*') ? 'staff' : (request()->is('users*') ? 'users' : null);
            if ($portal && tenancy()->initialized) {
                $tenantName = tenant('short_name') ?? tenant('name') ?? config('app.name');
                $appTitle = $portal === 'staff' ? "{$tenantName} Staff" : $tenantName;
                $themeColor = $portal === 'staff'
                    ? (tenant('staff_theme_color') ?? '#0f172a')
                    : (tenant('user_theme_color') ?? '#4f46e5');
                $appleIcon = $portal === 'staff' ? '/icons/staff-192x192.png' : '/icons/user-192x192.png';
            }
        @endphp

        @if(isset($portal) && $portal && tenancy()->initialized)
            <link rel="manifest" href="{{ route('pwa.manifest', ['portal' => $portal]) }}">
            <meta name="theme-color" content="{{ $themeColor }}">
            <meta name="apple-mobile-web-app-capable" content="yes">
            <meta name="apple-mobile-web-app-title" content="{{ $appTitle }}">
            <link rel="apple-touch-icon" href="{{ $appleIcon }}">
        @endif

        @vite(['resources/css/app.css', 'resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        <x-inertia::head>
            <title>{{ config('app.name', 'Laravel') }}</title>
        </x-inertia::head>
    </head>
    <body class="font-sans antialiased">
        <x-inertia::app />
    </body>
</html>
