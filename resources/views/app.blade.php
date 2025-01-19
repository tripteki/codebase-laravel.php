<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />
    @vite("resources/css/app.css")
    @vite("resources/js/app.js")
    @inertiaHead
</head>

<body>
    @routes
    @inertia
</body>

</html>
