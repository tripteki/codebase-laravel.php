@extends('auth::layouts.mail')

@section('title', __($headingKey))

@section('header_badge')
    {{ $appName }}
@endsection

@section('content')
    <p style="margin:0 0 6px;font-size:20px;font-weight:700;line-height:1.25;letter-spacing:-0.02em;color:#f9fafb;">{{ __($headingKey) }}</p>
    <p style="margin:0 0 12px;font-size:15px;font-weight:700;line-height:1.55;color:#f3f4f6;">{{ __('auth.email_greeting', ['name' => $userName]) }}</p>
    <p style="margin:0 0 12px;color:#d1d5db;font-size:14px;line-height:1.55;">{{ __($lineKey) }}</p>
    @include('auth::emails.partials.account-panel', [
        'userName' => $userName,
        'userNameLabel' => $userNameLabel ?? null,
        'userEmail' => $userEmail,
    ])
    @include('auth::emails.partials.signature')
@endsection
