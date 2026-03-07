@php
    $app = $appName ?? config('app.name');
    $mailFooterDefault =
        '&copy; ' . date('Y') . ' ' . htmlspecialchars((string) $app, ENT_QUOTES, 'UTF-8');
    $primaryHex = '#2563eb';
    $secondaryHex = '#64748b';
    $tertiaryHex = '#94a3b8';
    $normalizeHex = static function (string $hex): string {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            return $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        return strlen($hex) === 6 && ctype_xdigit($hex) ? $hex : '2563eb';
    };
    $primaryHexNorm = $normalizeHex((string) $primaryHex);
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
            --brand-primary: {{ $primaryHex }};
            --brand-primary-rgb: {{ $primaryRgb }};
            --brand-secondary: {{ $secondaryHex }};
            --brand-tertiary: {{ $tertiaryHex }};
        }

        body {
            margin: 0;
            padding: 0;
            background: #f1f5f9;
            color: #0f172a;
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
        }
        a {
            color: var(--brand-primary);
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
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
            border-top: 4px solid var(--brand-primary);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }
        .header {
            padding: 20px 24px;
            background: var(--brand-primary);
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 12px;
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
            margin-left: auto;
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
        .mb-0 { margin-bottom: 0 !important; }
        .mb-2 { margin-bottom: 8px !important; }
        .mb-4 { margin-bottom: 16px !important; }
        .mt-4 { margin-top: 16px !important; }
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
        .pill {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 13px;
            background: #f1f5f9;
            color: #475569;
            font-weight: 500;
        }

        @media (max-width: 600px) {
            .wrapper { border-radius: 12px; }
            .content { padding: 18px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="wrapper">
            <div class="header">
                <span class="badge">@yield('header_badge', $app)</span>
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
