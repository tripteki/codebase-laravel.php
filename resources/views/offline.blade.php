<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ __("common.offline_title") }} - {{ \Illuminate\Support\Str::headline(config("app.name")) }}</title>

    @php
        $isTenancy = (bool) config('tenancy.is_tenancy');
        $SettingHelper = \App\Helpers\SettingHelper::class;
        $defaultPrimaryHex = ($p = $SettingHelper::get('COLOR_PRIMARY')) !== null ? trim((string) $p) : null;
        $defaultTertiaryHex = ($t = $SettingHelper::get('COLOR_TERTIARY')) !== null ? trim((string) $t) : null;
        $primaryHex = $isTenancy && hasTenant() && tenant('primary_color') ? (string) tenant('primary_color') : $defaultPrimaryHex;
        $tertiaryHex = $isTenancy && hasTenant() && tenant('tertiary_color') ? (string) tenant('tertiary_color') : $defaultTertiaryHex;
    @endphp
    <meta name="theme-color" content="{{ $primaryHex }}" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <style>
        :root {
            --tenant-primary: {{ $primaryHex }};
            --tenant-tertiary: {{ $tertiaryHex }};
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--tenant-primary) 0%, var(--tenant-tertiary) 100%);
            color: #fff;
            text-align: center;
            padding: 20px;
            -webkit-font-smoothing: antialiased;
        }

        .container {
            max-width: 500px;
            width: 100%;
        }

        .icon {
            font-size: 64px;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 32px;
            font-weight: 600;
            margin-bottom: 16px;
        }

        p {
            font-size: 18px;
            margin-bottom: 32px;
            opacity: 0.95;
            line-height: 1.6;
        }

        button {
            background-color: #ffffff;
            color: var(--tenant-primary);
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            font-weight: 600;
            font-family: inherit;
            border-radius: 8px;
            cursor: pointer;
            transition: filter 0.2s, box-shadow 0.2s;
        }

        button:hover {
            filter: brightness(0.96);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        button:focus-visible {
            outline: none;
            box-shadow: 0 0 0 2px color-mix(in srgb, var(--tenant-primary) 55%, #ffffff 45%);
        }

        button:active {
            filter: brightness(0.92);
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="icon">📡</div>
        <h1>{{ __("common.offline_title") }}</h1>
        <p>{{ __("common.offline_message") }}</p>
        <button type="button" onclick="window.location.reload()">{{ __("common.retry") }}</button>
    </div>
</body>

</html>
