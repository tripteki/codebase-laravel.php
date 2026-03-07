@php
    $SettingHelper = App\Helpers\SettingHelper::class;
    $app = $appName ?? config('app.name');
    $allRightsReserved = trim((string) $SettingHelper::get('ALL_RIGHTS_RESERVED', ''));
    $mailFooterDefault =
        '&copy; ' . date('Y') . ' ' . htmlspecialchars((string) $app, ENT_QUOTES, 'UTF-8')
        . ($allRightsReserved !== '' ? '. ' . htmlspecialchars($allRightsReserved, ENT_QUOTES, 'UTF-8') : '');
    $logoUrl = $logoUrl ?? asset('asset/logo.png');

    $defaultPrimaryHex = ($p = $SettingHelper::get('COLOR_PRIMARY')) !== null ? trim((string) $p) : null;
    $defaultSecondaryHex = ($s = $SettingHelper::get('COLOR_SECONDARY')) !== null ? trim((string) $s) : null;
    $defaultTertiaryHex = ($t = $SettingHelper::get('COLOR_TERTIARY')) !== null ? trim((string) $t) : null;
    $primaryHex = $primaryColor ?? $defaultPrimaryHex;
    $secondaryHex = $secondaryColor ?? $defaultSecondaryHex;
    $tertiaryHex = $tertiaryColor ?? $defaultTertiaryHex;
    $normalizeHex = static function (string $hex): string {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            return $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        return strlen($hex) === 6 && ctype_xdigit($hex) ? $hex : '2563eb';
    };
    $primaryHexNorm = $normalizeHex((string) ($primaryHex ?? ''));
    if (strlen($primaryHexNorm) !== 6 && $defaultPrimaryHex !== null && $defaultPrimaryHex !== '') {
        $primaryHexNorm = $normalizeHex(ltrim((string) $defaultPrimaryHex, '#'));
    }
    $primaryRgb = implode(',', [
        hexdec(substr($primaryHexNorm, 0, 2)),
        hexdec(substr($primaryHexNorm, 2, 2)),
        hexdec(substr($primaryHexNorm, 4, 2)),
    ]);
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $app)</title>
    <style>
        :root {
            --tenant-primary: {{ $primaryHex }};
            --tenant-primary-rgb: {{ $primaryRgb }};
            --tenant-secondary: {{ $secondaryHex }};
            --tenant-tertiary: {{ $tertiaryHex }};
        }

        body {
            margin: 0;
            padding: 0;
            background: #f1f5f9;
            color: #0f172a;
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
        }
        a {
            color: var(--tenant-primary);
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        img {
            border: 0;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        .container {
            width: 100%;
            padding: 24px 12px;
        }
        .wrapper {
            max-width: 640px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            border-top: 4px solid var(--tenant-primary);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }
        .header {
            padding: 20px 24px;
            background: var(--tenant-primary);
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.28),
                inset 0 -28px 56px -24px rgba(0, 0, 0, 0.2),
                inset 0 0 48px rgba(255, 255, 255, 0.07),
                inset 0 0 80px -20px rgba(var(--tenant-primary-rgb), 0.35);
        }
        .header img {
            filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.45))
                drop-shadow(0 0 20px rgba(255, 255, 255, 0.2));
        }
        .header .badge {
            max-width: 62%;
            text-align: right;
            line-height: 1.35;
            white-space: normal;
            padding: 6px 12px;
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.5);
            color: #ffffff;
            text-shadow:
                0 0 12px rgba(255, 255, 255, 0.65),
                0 0 24px rgba(255, 255, 255, 0.35),
                0 1px 2px rgba(0, 0, 0, 0.15);
            box-shadow:
                0 0 18px rgba(255, 255, 255, 0.25),
                0 0 28px rgba(var(--tenant-primary-rgb), 0.4),
                inset 0 1px 0 rgba(255, 255, 255, 0.35);
        }
        .content {
            padding: 24px;
            background: #ffffff;
        }
        .footer {
            padding: 18px 24px;
            text-align: center;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 13px;
        }
        .footer a {
            color: var(--tenant-primary);
        }

        h1 {
            margin: 0 0 8px 0;
            font-size: 22px;
        }
        p {
            margin: 0 0 12px 0;
        }

        .text-muted { color: #64748b !important; }
        .small { font-size: 13px; }
        .fw-bold { font-weight: 700; }
        .fw-semibold { font-weight: 600; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .whitespace-pre-wrap { white-space: pre-wrap; }
        .text-break { word-break: break-all; }
        .mb-0 { margin-bottom: 0 !important; }
        .mb-2 { margin-bottom: 8px !important; }
        .mb-3 { margin-bottom: 12px !important; }
        .mb-4 { margin-bottom: 16px !important; }
        .mt-3 { margin-top: 12px !important; }
        .mt-4 { margin-top: 16px !important; }
        .my-4 { margin-top: 16px !important; margin-bottom: 16px !important; }
        .p-2 { padding: 8px !important; }
        .p-3 { padding: 12px !important; }
        .panel {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #fbfcff;
        }
        .panel-account {
            padding: 20px 20px !important;
        }
        .panel-account-title {
            margin: 0 0 14px 0 !important;
            padding-bottom: 12px;
            text-align: center;
            font-size: 15px;
            border-bottom: 1px solid #e5e7eb;
        }
        .panel-account-body {
            text-align: left;
            color: #334155;
        }
        .panel-account-body .pill {
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }
        .btn {
            display: inline-block;
            border-radius: 999px;
            padding: 12px 18px;
            font-weight: 700;
            text-align: center;
        }
        .btn-primary {
            color: #fff !important;
            background: var(--tenant-primary);
        }
        .btn-primary:hover {
            opacity: 0.95;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 12px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #64748b;
            margin-left: auto;
        }
        .pill {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 13px;
            background: #f1f5f9;
            color: #475569;
            font-weight: 500;
        }
        .mb-1 { margin-bottom: 4px !important; }
        .px-3 { padding-left: 12px !important; padding-right: 12px !important; }
        .py-2 { padding-top: 8px !important; padding-bottom: 8px !important; }

        @media (max-width: 600px) {
            .wrapper { border-radius: 12px; }
            .content { padding: 18px; }
        }
    </style>
</head>
<body>
    @hasSection('preheader')
    <div style="display:none!important;visibility:hidden;mso-hide:all;font-size:1px;line-height:1px;color:#fff;max-height:0;max-width:0;opacity:0;overflow:hidden;">
        @yield('preheader')
    </div>
    @endif

    <div class="container">
        <div class="wrapper">
            <div class="header">
                <img src="{{ $logoUrl }}" alt="{{ $app }}" height="28" width="auto">
                @hasSection('header_badge')
                <span class="badge">@yield('header_badge')</span>
                @endif
            </div>

            <div class="content">
                @yield('content')
            </div>

            <div class="footer">
                @yield('footer', $mailFooterDefault)
            </div>
        </div>
    </div>
</body>
</html>
