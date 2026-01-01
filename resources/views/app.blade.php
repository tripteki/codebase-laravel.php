<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="manifest" href="{{ asset('manifest.json') }}" />
    <link rel="apple-touch-icon" href="{{ asset('asset/logo.png') }}" />
    <link rel="icon" type="image/png" href="{{ asset('asset/favicon.png') }}" />
    @viteReactRefresh
    @vite("resources/css/app.css")
    @vite("resources/js/app.tsx")
    @inertiaHead
</head>

<body>
    @routes
    @inertia

    <script type="text/javascript" src="{{ asset('sw.js') }}"></script>
</body>

</html>
