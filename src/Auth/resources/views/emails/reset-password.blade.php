@extends('auth::layouts.mail')

@section('title', __('auth.password_reset_subject'))

@section('header_badge')
    {{ $appName }}
@endsection

@section('content')
    <p style="margin:0 0 6px;font-size:20px;font-weight:700;line-height:1.25;letter-spacing:-0.02em;color:#f9fafb;">{{ __('auth.password_reset_heading') }}</p>
    <p style="margin:0 0 12px;font-size:15px;font-weight:700;line-height:1.55;color:#f3f4f6;">{{ __('auth.email_greeting', ['name' => $userName]) }}</p>
    <p style="margin:0 0 12px;color:#d1d5db;font-size:14px;line-height:1.55;">{{ __('auth.password_reset_line') }}</p>
    <p style="margin:0 0 12px;color:#9ca3af;font-size:12px;line-height:1.5;">{{ __('auth.password_reset_ignore') }}</p>
    @include('auth::emails.partials.account-panel', [
        'userName' => $userName,
        'userNameLabel' => $userNameLabel ?? null,
        'userEmail' => $userEmail,
    ])
    @if (! empty($resetLink))
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" align="center" style="margin:12px 0 10px;">
        <tr>
            <td align="center">
                <a class="mail-btn" href="{{ $resetLink }}" style="display:inline-block;min-width:180px;border-radius:8px;padding:11px 22px;font-size:13px;font-weight:700;letter-spacing:0.01em;text-align:center;color:#ffffff;background:{{ $primaryColor ?? '#2563eb' }};text-decoration:none;">{{ __('auth.password_reset_action') }}</a>
            </td>
        </tr>
    </table>
    @endif
    @include('auth::emails.partials.signature')
@endsection
