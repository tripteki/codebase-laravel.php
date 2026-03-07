<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover" />

    @php
        $baseAppName = \Illuminate\Support\Str::headline(config('app.name'));
        $tenantTitle = (config('tenancy.is_tenancy') && tenancy()->initialized) ? (tenant('title') ?: null) : null;
        $appName = $tenantTitle ?: $baseAppName;
        $pageTitle = $title ?? null;
    @endphp

    <title>
        @if ($pageTitle)
            {{ $pageTitle }} - {{ $appName }}
        @else
            @hasSection("title")
                @yield("title") - {{ $appName }}
            @else
                {{ $appName }}
            @endif
        @endif
    </title>

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#ffffff" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="default" />
    <meta name="apple-mobile-web-app-title" content="{{ $appName }}" />
    <meta name="application-name" content="{{ $appName }}" />
    <meta name="description" content="{{ $appName }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="vapid-key" content="{{ config('webpush.vapid.public_key') }}" />
    <meta name="webpush-subscribe-url" content="{{ tenant_routes('webpush.subscribe') }}" />

    <script>

        (function () {
            var el = document.documentElement;
            var def = el.getAttribute("data-default-theme") || "light";

            try {
                var stored = window.localStorage.getItem("theme");
                if (! stored && def === "dark") {
                    el.classList.add("dark");
                }
            } catch (e) {
                if (def === "dark") {
                    el.classList.add("dark");
                }
            }
        })();

    </script>

    <!-- Icons & Tenancy Colors -->
    @php
        $isTenancy = (bool) config('tenancy.is_tenancy');
        $defaultFaviconIco = asset('favicon.ico');
        $defaultFaviconPng = asset('asset/favicon.png');
        $defaultLogoPng = asset('asset/logo.png');

        $tenantFaviconIco = $isTenancy && hasTenant() && tenant('favicon_ico') ? asset('storage/' . tenant('favicon_ico')) : $defaultFaviconIco;
        $tenantFaviconPng = $isTenancy && hasTenant() && tenant('favicon_png') ? asset('storage/' . tenant('favicon_png')) : $defaultFaviconPng;
        $tenantLogoPng = $isTenancy && hasTenant() && tenant('icon') ? asset('storage/' . tenant('icon')) : $defaultLogoPng;

        $SettingHelper = \App\Helpers\SettingHelper::class;
        $defaultPrimaryHex = ($p = $SettingHelper::get('COLOR_PRIMARY')) !== null ? trim((string) $p) : null;
        $defaultSecondaryHex = ($s = $SettingHelper::get('COLOR_SECONDARY')) !== null ? trim((string) $s) : null;
        $defaultTertiaryHex = ($t = $SettingHelper::get('COLOR_TERTIARY')) !== null ? trim((string) $t) : null;

        $primaryHex = $isTenancy && hasTenant() && tenant('primary_color') ? (string) tenant('primary_color') : $defaultPrimaryHex;
        $secondaryHex = $isTenancy && hasTenant() && tenant('secondary_color') ? (string) tenant('secondary_color') : $defaultSecondaryHex;
        $tertiaryHex = $isTenancy && hasTenant() && tenant('tertiary_color') ? (string) tenant('tertiary_color') : $defaultTertiaryHex;
    @endphp

    <link rel="manifest" href="{{ tenant_routes('manifest') }}" />
    <link rel="icon" type="image/x-icon" href="{{ $tenantFaviconIco }}" />
    <link rel="icon" type="image/png" sizes="128x128" href="{{ $tenantFaviconPng }}" />
    <link rel="icon" type="image/png" sizes="512x512" href="{{ $tenantLogoPng }}" />
    <link rel="apple-touch-icon" sizes="512x512" href="{{ $tenantLogoPng }}" />

    <!-- PWA Splash Screens for iOS -->
    <link rel="apple-touch-startup-image" media="screen and (device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)" href="{{ $tenantLogoPng }}" />

    <!-- Tenancy-aware colors overriding default CSS theme -->
    <style>

        :root {
            --tenant-primary: {{ $primaryHex }};
            --tenant-secondary: {{ $secondaryHex }};
            --tenant-tertiary: {{ $tertiaryHex }};
        }

        :root {
            --primary: var(--tenant-primary);
            --secondary: var(--tenant-secondary);
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, var(--tenant-primary) 0%, var(--tenant-tertiary) 100%);
        }

        .bg-gradient-secondary {
            background: linear-gradient(135deg, var(--tenant-secondary) 0%, hsl(138.8, 100%, 96.9%) 100%);
        }

        .bg-gradient-primary-secondary,
        .bg-gradient-blue-green {
            background: linear-gradient(135deg, var(--tenant-primary) 0%, var(--tenant-secondary) 100%);
        }

        .bg-gradient-dark-blue {
            background: linear-gradient(135deg, hsl(229.1, 95.7%, 9%) 0%, var(--tenant-tertiary) 100%);
        }

        .text-gradient-primary {
            background: linear-gradient(135deg, var(--tenant-primary) 0%, var(--tenant-tertiary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .text-gradient-secondary {
            background: linear-gradient(135deg, var(--tenant-secondary) 0%, hsl(138.8, 100%, 96.9%) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .text-gradient-tertiary {
            background: linear-gradient(135deg, var(--tenant-tertiary) 0%, hsl(219.5, 95.2%, 24.7%) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .text-gradient-primary-secondary {
            background: linear-gradient(135deg, var(--tenant-primary) 0%, var(--tenant-secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .auth-text {
            background: linear-gradient(
                90deg,
                #ffffff 0%,
                #ffffff 20%,
                var(--tenant-secondary) 35%,
                var(--tenant-primary) 50%,
                var(--tenant-secondary) 65%,
                #ffffff 80%,
                #ffffff 100%
            );
            background-size: 200% 100%;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .sidebar-active-pill {
            background: linear-gradient(135deg, var(--tenant-secondary) 0%, var(--tenant-primary) 60%, var(--tenant-tertiary) 100%);
        }

        .auth-cta {
            background-color: var(--tenant-secondary);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
        }

        .auth-cta:hover {
            filter: brightness(0.98);
        }

        .auth-cta:focus-visible {
            outline: none;
            box-shadow: 0 0 0 2px color-mix(in srgb, var(--tenant-secondary) 60%, #ffffff 40%);
        }

        .btn-primary {
            background-color: var(--tenant-primary);
            color: #ffffff;
        }

        .btn-primary:hover {
            filter: brightness(0.96);
        }

        .btn-primary:focus-visible {
            outline: none;
            box-shadow: 0 0 0 2px color-mix(in srgb, var(--tenant-primary) 55%, #ffffff 45%);
        }

        .btn-primary-soft {
            background-color: color-mix(in srgb, var(--tenant-primary) 10%, #ffffff 90%);
            color: var(--tenant-primary);
        }

        .btn-primary-soft:hover {
            background-color: color-mix(in srgb, var(--tenant-primary) 18%, #ffffff 82%);
        }

        .btn-primary-soft:focus-visible {
            outline: none;
            box-shadow: 0 0 0 2px color-mix(in srgb, var(--tenant-primary) 40%, #ffffff 60%);
        }

        .badge-primary {
            background-color: color-mix(in srgb, var(--tenant-primary) 15%, #ffffff 85%);
            color: var(--tenant-primary);
        }

        .badge-primary-dark {
            background-color: color-mix(in srgb, var(--tenant-primary) 25%, #020617 75%);
            color: var(--tenant-primary);
        }

        .badge-secondary {
            background-color: color-mix(in srgb, var(--tenant-secondary) 15%, #ffffff 85%);
            color: var(--tenant-secondary);
        }

        .badge-secondary-dark {
            background-color: color-mix(in srgb, var(--tenant-secondary) 25%, #020617 75%);
            color: var(--tenant-secondary);
        }

        .badge-tertiary {
            background-color: color-mix(in srgb, var(--tenant-tertiary) 15%, #ffffff 85%);
            color: var(--tenant-tertiary);
        }

        .badge-tertiary-dark {
            background-color: color-mix(in srgb, var(--tenant-tertiary) 25%, #020617 75%);
            color: var(--tenant-tertiary);
        }

        .btn-secondary {
            background-color: var(--tenant-secondary);
            color: #ffffff;
        }

        .btn-secondary:hover {
            filter: brightness(0.96);
        }

        .btn-secondary:focus-visible {
            outline: none;
            box-shadow: 0 0 0 2px color-mix(in srgb, var(--tenant-secondary) 55%, #ffffff 45%);
        }

        .btn-secondary-soft {
            background-color: color-mix(in srgb, var(--tenant-secondary) 10%, #ffffff 90%);
            color: var(--tenant-secondary);
        }

        .btn-secondary-soft:hover {
            background-color: color-mix(in srgb, var(--tenant-secondary) 18%, #ffffff 82%);
        }

        .btn-secondary-soft:focus-visible {
            outline: none;
            box-shadow: 0 0 0 2px color-mix(in srgb, var(--tenant-secondary) 40%, #ffffff 60%);
        }

        .btn-tertiary {
            background-color: var(--tenant-tertiary);
            color: #ffffff;
        }

        .btn-tertiary:hover {
            filter: brightness(1.05);
        }

        .btn-tertiary:focus-visible {
            outline: none;
            box-shadow: 0 0 0 2px color-mix(in srgb, var(--tenant-tertiary) 55%, #ffffff 45%);
        }

        .btn-tertiary-soft {
            background-color: color-mix(in srgb, var(--tenant-tertiary) 10%, #ffffff 90%);
            color: var(--tenant-tertiary);
        }

        .btn-tertiary-soft:hover {
            background-color: color-mix(in srgb, var(--tenant-tertiary) 18%, #ffffff 82%);
        }

        .btn-tertiary-soft:focus-visible {
            outline: none;
            box-shadow: 0 0 0 2px color-mix(in srgb, var(--tenant-tertiary) 40%, #ffffff 60%);
        }

        .tab-primary-active {
            color: var(--tenant-primary);
            background-color: color-mix(in srgb, var(--tenant-primary) 10%, #ffffff 90%);
            border-color: color-mix(in srgb, var(--tenant-primary) 30%, #e5e7eb 70%);
        }

        .dark .tab-primary-active {
            color: var(--tenant-primary);
            background-color: color-mix(in srgb, var(--tenant-primary) 20%, #020617 80%);
            border-color: color-mix(in srgb, var(--tenant-primary) 45%, #020617 55%);
        }

        .tab-primary-hover {
            transition: background-color 150ms ease-out, color 150ms ease-out, border-color 150ms ease-out;
        }

        .tab-primary-hover:hover {
            background-color: color-mix(in srgb, var(--tenant-primary) 10%, #f9fafb 90%);
            color: var(--tenant-primary);
            border-color: color-mix(in srgb, var(--tenant-primary) 25%, #e5e7eb 75%);
        }

        .dark .tab-primary-hover:hover {
            background-color: color-mix(in srgb, var(--tenant-primary) 25%, #020617 75%);
            color: var(--tenant-primary);
            border-color: color-mix(in srgb, var(--tenant-primary) 45%, #020617 55%);
        }

        .tab-secondary-active {
            color: var(--tenant-secondary);
            background-color: color-mix(in srgb, var(--tenant-secondary) 10%, #ffffff 90%);
            border-color: color-mix(in srgb, var(--tenant-secondary) 30%, #e5e7eb 70%);
        }

        .dark .tab-secondary-active {
            color: var(--tenant-secondary);
            background-color: color-mix(in srgb, var(--tenant-secondary) 20%, #020617 80%);
            border-color: color-mix(in srgb, var(--tenant-secondary) 45%, #020617 55%);
        }

        .tab-secondary-hover {
            transition: background-color 150ms ease-out, color 150ms ease-out, border-color 150ms ease-out;
        }

        .tab-secondary-hover:hover {
            background-color: color-mix(in srgb, var(--tenant-secondary) 10%, #f9fafb 90%);
            color: var(--tenant-secondary);
            border-color: color-mix(in srgb, var(--tenant-secondary) 25%, #e5e7eb 75%);
        }

        .dark .tab-secondary-hover:hover {
            background-color: color-mix(in srgb, var(--tenant-secondary) 25%, #020617 75%);
            color: var(--tenant-secondary);
            border-color: color-mix(in srgb, var(--tenant-secondary) 45%, #020617 55%);
        }

        .tab-tertiary-active {
            color: var(--tenant-tertiary);
            background-color: color-mix(in srgb, var(--tenant-tertiary) 10%, #ffffff 90%);
            border-color: color-mix(in srgb, var(--tenant-tertiary) 30%, #e5e7eb 70%);
        }

        .dark .tab-tertiary-active {
            color: var(--tenant-tertiary);
            background-color: color-mix(in srgb, var(--tenant-tertiary) 20%, #020617 80%);
            border-color: color-mix(in srgb, var(--tenant-tertiary) 45%, #020617 55%);
        }

        .tab-tertiary-hover {
            transition: background-color 150ms ease-out, color 150ms ease-out, border-color 150ms ease-out;
        }

        .tab-tertiary-hover:hover {
            background-color: color-mix(in srgb, var(--tenant-tertiary) 10%, #f9fafb 90%);
            color: var(--tenant-tertiary);
            border-color: color-mix(in srgb, var(--tenant-tertiary) 25%, #e5e7eb 75%);
        }

        .dark .tab-tertiary-hover:hover {
            background-color: color-mix(in srgb, var(--tenant-tertiary) 25%, #020617 75%);
            color: var(--tenant-tertiary);
            border-color: color-mix(in srgb, var(--tenant-tertiary) 45%, #020617 55%);
        }

        .link-primary {
            color: var(--tenant-primary);
        }

        .link-primary:hover {
            color: color-mix(in srgb, var(--tenant-primary) 70%, #ffffff 30%);
        }

        .link-secondary {
            color: var(--tenant-secondary);
        }

        .link-secondary:hover {
            color: color-mix(in srgb, var(--tenant-secondary) 70%, #ffffff 30%);
        }

        .link-tertiary {
            color: var(--tenant-tertiary);
        }

        .link-tertiary:hover {
            color: color-mix(in srgb, var(--tenant-tertiary) 70%, #ffffff 30%);
        }

        .input-primary {
            border-color: color-mix(in srgb, var(--tenant-primary) 30%, #e5e7eb 70%);
        }

        .input-primary:focus,
        input.input-primary:focus {
            outline: none;
            border-color: var(--tenant-primary);
            box-shadow: 0 0 0 2px color-mix(in srgb, var(--tenant-primary) 55%, #ffffff 45%);
        }

        .input-primary.search-input:focus,
        input.input-primary.search-input:focus {
            outline: none;
            border-color: color-mix(in srgb, var(--tenant-primary) 30%, #e5e7eb 70%);
            box-shadow: none;
        }

        .dark .input-primary.search-input:focus,
        .dark input.input-primary.search-input:focus {
            border-color: color-mix(in srgb, var(--tenant-primary) 45%, #020617 55%);
        }

        .dark .input-primary {
            border-color: color-mix(in srgb, var(--tenant-primary) 45%, #020617 55%);
        }

        .input-secondary {
            border-color: color-mix(in srgb, var(--tenant-secondary) 30%, #e5e7eb 70%);
        }

        .input-secondary:focus,
        input.input-secondary:focus {
            outline: none;
            border-color: var(--tenant-secondary);
            box-shadow: 0 0 0 2px color-mix(in srgb, var(--tenant-secondary) 55%, #ffffff 45%);
        }

        .dark .input-secondary {
            border-color: color-mix(in srgb, var(--tenant-secondary) 45%, #020617 55%);
        }

        .input-tertiary {
            border-color: color-mix(in srgb, var(--tenant-tertiary) 30%, #e5e7eb 70%);
        }

        .input-tertiary:focus,
        input.input-tertiary:focus {
            outline: none;
            border-color: var(--tenant-tertiary);
            box-shadow: 0 0 0 2px color-mix(in srgb, var(--tenant-tertiary) 55%, #ffffff 45%);
        }

        .dark .input-tertiary {
            border-color: color-mix(in srgb, var(--tenant-tertiary) 45%, #020617 55%);
        }

        input[type="checkbox"].checkbox-primary,
        input[type="checkbox"].checkbox-secondary,
        input[type="checkbox"].checkbox-tertiary {
            -webkit-appearance: none;
            appearance: none;
            background-color: #f9fafb;
            border: 1px solid #d1d5db;
            background-size: 100%;
            background-repeat: no-repeat;
            background-position: center;
        }

        .dark input[type="checkbox"].checkbox-primary,
        .dark input[type="checkbox"].checkbox-secondary,
        .dark input[type="checkbox"].checkbox-tertiary {
            background-color: #4b5563;
            border-color: #6b7280;
        }

        input[type="checkbox"].checkbox-primary,
        .checkbox-primary {
            color: var(--tenant-primary);
            accent-color: var(--tenant-primary) !important;
        }

        input[type="checkbox"].checkbox-primary:checked,
        .checkbox-primary:checked {
            background-color: var(--tenant-primary) !important;
            border-color: var(--tenant-primary) !important;
            background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e") !important;
        }

        .checkbox-primary:focus-visible {
            outline: none;
            box-shadow: 0 0 0 2px color-mix(in srgb, var(--tenant-primary) 55%, #ffffff 45%);
        }

        input[type="checkbox"].checkbox-secondary,
        .checkbox-secondary {
            color: var(--tenant-secondary);
            accent-color: var(--tenant-secondary) !important;
        }

        input[type="checkbox"].checkbox-secondary:checked,
        .checkbox-secondary:checked {
            background-color: var(--tenant-secondary) !important;
            border-color: var(--tenant-secondary) !important;
            background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e") !important;
        }

        .checkbox-secondary:focus-visible {
            outline: none;
            box-shadow: 0 0 0 2px color-mix(in srgb, var(--tenant-secondary) 55%, #ffffff 45%);
        }

        input[type="checkbox"].checkbox-tertiary,
        .checkbox-tertiary {
            color: var(--tenant-tertiary);
            accent-color: var(--tenant-tertiary) !important;
        }

        input[type="checkbox"].checkbox-tertiary:checked,
        .checkbox-tertiary:checked {
            background-color: var(--tenant-tertiary) !important;
            border-color: var(--tenant-tertiary) !important;
            background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e") !important;
        }

        .checkbox-tertiary:focus-visible {
            outline: none;
            box-shadow: 0 0 0 2px color-mix(in srgb, var(--tenant-tertiary) 55%, #ffffff 45%);
        }

        .inline-datepicker .datepicker-cell.selected {
            background-color: var(--tenant-primary) !important;
            color: white !important;
        }

        .dark .inline-datepicker .datepicker-cell.selected {
            background-color: var(--tenant-primary) !important;
            color: white !important;
        }

        .auth-hero-bg {
            background:
                radial-gradient(600px 300px at 100% 0%, rgba(255, 255, 255, 0.18), transparent 40%),
                linear-gradient(
                    180deg,
                    var(--tenant-primary, hsl(213.5, 96.4%, 44.1%)) 0%,
                    var(--tenant-primary, hsl(213.5, 96.4%, 44.1%)) 50%,
                    var(--tenant-tertiary, hsl(219.5, 95.2%, 24.7%)) 100%
                );
        }

        @media (min-width: 1024px) {
            .auth-hero-bg {
                background:
                    radial-gradient(1200px 600px at 100% 0%, rgba(255, 255, 255, 0.18), transparent 40%),
                    linear-gradient(
                        180deg,
                        var(--tenant-primary, hsl(213.5, 96.4%, 44.1%)) 0%,
                        var(--tenant-primary, hsl(213.5, 96.4%, 44.1%)) 50%,
                        var(--tenant-tertiary, hsl(219.5, 95.2%, 24.7%)) 100%
                    );
            }
        }

        .dark .auth-hero-bg {
            background:
                radial-gradient(600px 300px at 100% 0%, rgba(255, 255, 255, 0.10), transparent 40%),
                linear-gradient(180deg, hsl(220, 50%, 14%) 0%, hsl(220, 50%, 18%) 50%, hsl(220, 50%, 10%) 100%);
        }

        @media (min-width: 1024px) {
            .dark .auth-hero-bg {
                background:
                    radial-gradient(1200px 600px at 100% 0%, rgba(255, 255, 255, 0.10), transparent 40%),
                    linear-gradient(180deg, hsl(220, 50%, 14%) 0%, hsl(220, 50%, 18%) 50%, hsl(220, 50%, 10%) 100%);
            }
        }

    </style>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @livewireStyles
    <link href="{{ asset('vendor/tailwind/typography.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('vendor/tailwind/flowbite.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/app.css') }}" rel="stylesheet" />
    @php
        $layoutShowSidebar = $showSidebar ?? request()->routeIs('admin.*');
    @endphp

    @if ($layoutShowSidebar)
        <link href="{{ asset('css/components/sidebar.css') }}" rel="stylesheet" />
    @endif
    @stack("styles")

    <!-- Scripts -->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/tailwind/tailwind.min.js') }}"></script>
    <script>tailwind.config = { darkMode: "class", };</script>
    @stack("scripts-start")
</head>

<body class="font-sans antialiased bg-gray-100 text-gray-900 dark:bg-gray-900 dark:text-gray-100 transition-colors">
    @include('components.toast')

    @auth
        <livewire:admin.notification.realtime-notification-component />
    @endauth

    @hasSection("content")
        @yield("content")
    @else
        @if ($layoutShowSidebar ?? false)
            <div wire:ignore id="layout-admin-sidebar">
                @include("components.admin.sidebar")
            </div>
        @endif
        {{ $slot ?? "" }}
    @endif

    @livewireScripts
    <script src="{{ asset('vendor/tailwind/flowbite.min.js') }}"></script>
    <script src="{{ asset('vendor/tailwind/apexcharts.min.js') }}"></script>
    <script src="{{ asset('vendor/tailwind/datatables.min.js') }}"></script>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/pwa.js') }}" defer></script>
    <script src="{{ asset('js/wysiwyg.js') }}"></script>
    @if ($layoutShowSidebar ?? false)
        <script src="{{ asset('js/components/sidebar.js') }}"></script>
    @endif
    @stack("scripts-end")
</body>

</html>
