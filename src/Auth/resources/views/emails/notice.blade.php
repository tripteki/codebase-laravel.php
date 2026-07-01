@extends('auth::layouts.mail')

@section('title', $heading)

@section('header_badge')
    {{ $appName }}
@endsection

@section('content')
    <p style="margin:0 0 6px;font-size:20px;font-weight:700;line-height:1.25;letter-spacing:-0.02em;color:#f9fafb;">{{ $heading }}</p>
    <p style="margin:0 0 12px;font-size:15px;font-weight:700;line-height:1.55;color:#f3f4f6;">{{ __('auth.email_greeting', ['name' => $userName]) }}</p>
    <p style="margin:0 0 12px;color:#d1d5db;font-size:14px;line-height:1.55;">{{ $line }}</p>
    @if (! empty($line2))
    <p style="margin:0 0 12px;color:#9ca3af;font-size:12px;line-height:1.5;">{{ $line2 }}</p>
    @endif
    @include('auth::emails.partials.account-panel', [
        'userName' => $userName,
        'userEmail' => $userEmail,
    ])
    @if (! empty($actionUrl) && ! empty($actionLabel))
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" align="center" style="margin:12px 0 10px;">
        <tr>
            <td align="center">
                @if (! empty($secondaryUrl) && ! empty($secondaryLabel))
                <a class="mail-btn" href="{{ $actionUrl }}" style="display:inline-block;min-width:140px;border-radius:8px;padding:11px 22px;margin:4px;font-size:13px;font-weight:700;letter-spacing:0.01em;text-align:center;color:#ffffff;background:{{ $primaryColor ?? '#2563eb' }};text-decoration:none;">{{ $actionLabel }}</a>
                <a class="mail-btn" href="{{ $secondaryUrl }}" style="display:inline-block;min-width:140px;border-radius:8px;padding:10px 21px;margin:4px;font-size:13px;font-weight:700;letter-spacing:0.01em;text-align:center;color:#f9fafb;background:transparent;border:1px solid #4b5563;text-decoration:none;">{{ $secondaryLabel }}</a>
                @else
                <a class="mail-btn" href="{{ $actionUrl }}" style="display:inline-block;min-width:180px;border-radius:8px;padding:11px 22px;font-size:13px;font-weight:700;letter-spacing:0.01em;text-align:center;color:#ffffff;background:{{ $primaryColor ?? '#2563eb' }};text-decoration:none;">{{ $actionLabel }}</a>
                @endif
            </td>
        </tr>
    </table>
    @endif
    @include('auth::emails.partials.signature')
@endsection