@extends('auth::layouts.mail')

@section('title', __('auth.password_reset_subject'))

@section('preheader')
    {{ __('auth.password_reset_preheader', ['app' => $appName]) }}
@endsection

@section('header_badge')
    {{ $appName }}
@endsection

@section('content')
    @include('auth::emails.partials.hero', [
        'icon' => '🔐',
        'tone' => 'warning',
        'title' => __('auth.password_reset_heading'),
        'subtitle' => __('auth.password_reset_line'),
    ])

    @include('auth::emails.partials.greeting', ['userName' => $userName])

    @include('auth::emails.partials.account-panel', [
        'userName' => $userName,
        'userEmail' => $userEmail,
    ])

    @include('auth::emails.partials.cta', [
        'url' => $resetLink ?? null,
        'label' => __('auth.password_reset_action'),
    ])

    @include('auth::emails.partials.security-note', [
        'body' => __('auth.password_reset_ignore'),
    ])

    @include('auth::emails.partials.url-fallback', [
        'url' => $resetLink ?? null,
    ])

    @include('auth::emails.partials.signature')
@endsection
