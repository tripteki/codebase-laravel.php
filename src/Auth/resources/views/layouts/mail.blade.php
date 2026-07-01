@php
    $mailFooterDefault = '© ' . date('Y') . ' ' . ($displayName ?? $appName);
    $logoUrl = $logoUrl ?? frontend_url('manifest/asset/logo.png');

    $defaultPrimaryHex = '#2563eb';
    $defaultSecondaryHex = '#84cc16';
    $defaultTertiaryHex = '#1e3a8a';
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
    $hexToRgb = static function (string $hex) use ($normalizeHex): string {
        $normalized = $normalizeHex($hex);

        return implode(',', [
            hexdec(substr($normalized, 0, 2)),
            hexdec(substr($normalized, 2, 2)),
            hexdec(substr($normalized, 4, 2)),
        ]);
    };
    $primaryRgb = $hexToRgb((string) $primaryHex);
    $secondaryRgb = $hexToRgb((string) $secondaryHex);
    $tertiaryRgb = $hexToRgb((string) $tertiaryHex);
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $appName)</title>
    <style>
        :root {
            --brand-primary: {{ $primaryHex }};
            --brand-primary-rgb: {{ $primaryRgb }};
            --brand-secondary: {{ $secondaryHex }};
            --brand-secondary-rgb: {{ $secondaryRgb }};
            --brand-tertiary: {{ $tertiaryHex }};
            --brand-tertiary-rgb: {{ $tertiaryRgb }};
        }

        body {
            margin: 0;
            padding: 0;
            background:
                radial-gradient(900px 420px at 100% 0%, rgba(var(--brand-primary-rgb), 0.12), transparent 55%),
                #eef2f7;
            color: #0f172a;
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        a {
            color: var(--brand-primary);
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
            padding: 32px 14px;
        }
        .wrapper {
            max-width: 620px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            box-shadow:
                0 18px 42px rgba(15, 23, 42, 0.08),
                0 2px 8px rgba(15, 23, 42, 0.04);
            overflow: hidden;
        }
        .header {
            padding: 22px 26px;
            background:
                linear-gradient(
                    135deg,
                    var(--brand-primary) 0%,
                    color-mix(in srgb, var(--brand-primary) 58%, var(--brand-secondary) 42%) 52%,
                    color-mix(in srgb, var(--brand-tertiary) 88%, #ffffff 12%) 100%
                );
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.28),
                inset 0 -28px 56px -24px rgba(0, 0, 0, 0.18);
        }
        .header img {
            height: 32px;
            width: auto;
            filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.45))
                drop-shadow(0 0 20px rgba(255, 255, 255, 0.2));
        }
        .header .badge {
            max-width: 58%;
            text-align: right;
            line-height: 1.3;
            letter-spacing: 0.04em;
            font-size: 11px;
            font-weight: 700;
            white-space: normal;
            padding: 7px 12px;
            background: rgba(255, 255, 255, 0.16);
            border: 1px solid rgba(255, 255, 255, 0.42);
            border-radius: 999px;
            color: #ffffff;
            margin-left: auto;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.12);
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.28),
                0 8px 20px rgba(0, 0, 0, 0.08);
        }
        .content {
            padding: 28px 28px 24px;
            background: #ffffff;
        }
        .footer {
            padding: 20px 26px 22px;
            text-align: center;
            background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
            border-top: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 12px;
            letter-spacing: 0.01em;
        }
        .footer a {
            color: var(--brand-primary);
        }

        h1 {
            margin: 0 0 8px 0;
            font-size: 24px;
            line-height: 1.25;
            letter-spacing: -0.02em;
        }
        p {
            margin: 0 0 12px 0;
            line-height: 1.6;
        }

        .text-muted { color: #64748b !important; }
        .small { font-size: 13px; line-height: 1.55; }
        .fw-bold { font-weight: 700; }
        .fw-semibold { font-weight: 600; }
        .text-center { text-align: center; }
        .text-break { word-break: break-all; }
        .mb-0 { margin-bottom: 0 !important; }
        .mb-1 { margin-bottom: 4px !important; }
        .mb-2 { margin-bottom: 8px !important; }
        .mb-4 { margin-bottom: 18px !important; }
        .mt-4 { margin-top: 18px !important; }

        .email-hero {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 18px 18px 16px;
            border-radius: 16px;
            border: 1px solid #e8edf5;
            background:
                linear-gradient(180deg, rgba(var(--brand-primary-rgb), 0.07) 0%, rgba(255, 255, 255, 0.96) 100%);
        }
        .email-hero--success {
            background:
                linear-gradient(180deg, color-mix(in srgb, var(--brand-secondary) 16%, #ffffff) 0%, #ffffff 100%);
        }
        .email-hero--warning {
            background:
                linear-gradient(180deg, color-mix(in srgb, var(--brand-tertiary) 14%, #ffffff) 0%, #ffffff 100%);
        }
        .email-hero__icon {
            flex: 0 0 44px;
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            background: rgba(var(--brand-primary-rgb), 0.1);
        }
        .email-hero__copy {
            min-width: 0;
            flex: 1;
        }
        .email-hero__title {
            color: #0f172a;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.03em;
        }
        .email-hero__subtitle {
            margin-top: 6px;
            color: #475569;
            font-size: 14px;
        }
        .email-greeting {
            color: #0f172a;
            font-size: 16px;
        }
        .email-lead {
            color: #334155;
            font-size: 15px;
        }

        .panel {
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            background: #fbfcff;
            overflow: hidden;
        }
        .panel-account {
            padding: 0 !important;
        }
        .panel-account-title {
            margin: 0 !important;
            padding: 14px 18px;
            text-align: left;
            font-size: 12px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #64748b;
            border-bottom: 1px solid #e8edf5;
            background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
        }
        .panel-account-body {
            padding: 8px 10px 10px;
            color: #334155;
        }
        .info-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 10px 8px;
            border-bottom: 1px solid #eef2f7;
        }
        .info-row:last-child {
            border-bottom: 0;
        }
        .info-label {
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
            white-space: nowrap;
        }
        .info-value {
            font-size: 14px;
            font-weight: 600;
            color: #0f172a;
            text-align: right;
        }
        .panel-note {
            padding: 0 !important;
        }
        .note-box {
            border-left: 4px solid var(--brand-primary);
            background: linear-gradient(90deg, rgba(var(--brand-primary-rgb), 0.07) 0%, #fbfcff 38%);
        }
        .note-box__title {
            padding: 14px 16px 0;
            color: #0f172a;
        }
        .note-box .text-muted {
            padding: 0 16px 14px !important;
        }

        .cta-wrap {
            width: 100%;
            margin: 22px 0 24px;
        }
        .cta-cell {
            padding: 0;
        }
        .btn {
            display: inline-block;
            min-width: 220px;
            border-radius: 999px;
            padding: 14px 28px;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.01em;
            text-align: center;
        }
        .btn-primary {
            color: #fff !important;
            background: linear-gradient(
                135deg,
                var(--brand-primary) 0%,
                color-mix(in srgb, var(--brand-primary) 72%, var(--brand-secondary) 28%) 100%
            );
            box-shadow:
                0 12px 28px rgba(var(--brand-primary-rgb), 0.28),
                inset 0 1px 0 rgba(255, 255, 255, 0.22);
        }
        .btn-primary:hover {
            opacity: 0.96;
            text-decoration: none;
        }
        .pill {
            display: inline-block;
            padding: 5px 11px;
            border-radius: 999px;
            font-size: 13px;
            background: color-mix(in srgb, var(--brand-primary) 10%, #ffffff);
            color: color-mix(in srgb, var(--brand-primary) 78%, #0f172a);
            border: 1px solid color-mix(in srgb, var(--brand-primary) 24%, #ffffff);
            font-weight: 600;
        }
        .url-fallback {
            margin-top: 4px;
        }
        .url-box {
            margin: 0;
            padding: 12px 14px;
            border-radius: 12px;
            border: 1px dashed #cbd5e1;
            background: #f8fafc;
            color: #475569;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 12px;
            line-height: 1.5;
        }
        .email-signature {
            padding-top: 18px;
            border-top: 1px solid #eef2f7;
        }
        .signature-brand {
            font-size: 15px;
            letter-spacing: 0.08em;
            color: #0f172a;
        }

        @media (max-width: 600px) {
            .container { padding: 18px 10px; }
            .wrapper { border-radius: 16px; }
            .content { padding: 22px 18px 20px; }
            .header { padding: 18px 18px; }
            .header .badge { max-width: 52%; font-size: 10px; }
            .email-hero { padding: 16px 14px; }
            .email-hero__title { font-size: 21px; }
            .info-row {
                flex-direction: column;
                align-items: flex-start;
            }
            .info-value { text-align: left; }
            .btn { min-width: 100%; box-sizing: border-box; }
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
                <img src="{{ $logoUrl }}" alt="{{ $appName }}" height="32" width="auto">
                @hasSection('header_badge')
                <span class="badge">@yield('header_badge')</span>
                @endif
            </div>

            <div class="content">
                @yield('content')
            </div>

            <div class="footer">
                @yield('footer', e($mailFooterDefault))
            </div>
        </div>
    </div>
</body>
</html>
