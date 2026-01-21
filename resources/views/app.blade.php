<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover" />

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#ffffff" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="default" />
    <meta name="apple-mobile-web-app-title" content="{{ \Illuminate\Support\Str::headline(config('app.name')) }}" />
    <meta name="application-name" content="{{ \Illuminate\Support\Str::headline(config('app.name')) }}" />
    <meta name="description" content="{{ \Illuminate\Support\Str::headline(config('app.name')) }}" />

    <!-- Icons -->
    <link rel="manifest" href="{{ route('manifest') }}" />
    <link rel="icon" type="image/png" sizes="128x128" href="{{ asset('asset/favicon.png') }}" />
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('asset/logo.png') }}" />
    <link rel="apple-touch-icon" sizes="512x512" href="{{ asset('asset/logo.png') }}" />

    <!-- PWA Splash Screens for iOS -->
    <link rel="apple-touch-startup-image" media="screen and (device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)" href="{{ asset('asset/logo.png') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @vite("resources/css/app.css")
    @vite("resources/js/app.ts")
    @inertiaHead
</head>

<body class="font-sans antialiased">
    @routes
    @inertia
</body>

</html>
