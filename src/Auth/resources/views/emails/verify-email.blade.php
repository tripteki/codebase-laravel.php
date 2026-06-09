@extends('auth::layouts.mail')

@section('title', __('auth.email_verification_subject'))

@section('preheader')
    {{ __('auth.verify_email_preheader', ['app' => $appName]) }}
@endsection

@section('header_badge')
    {{ $appName }}
@endsection

@section('content')
    @include('auth::emails.partials.hero', [
        'icon' => '✉️',
        'tone' => 'primary',
        'title' => __('auth.verify_email_heading'),
        'subtitle' => __('auth.verify_email_line'),
    ])

    @include('auth::emails.partials.greeting', ['userName' => $userName])

    @include('auth::emails.partials.cta', [
        'url' => $verificationUrl ?? null,
        'label' => __('auth.verify_email_action'),
    ])

    @include('auth::emails.partials.account-panel', [
        'userName' => $userName,
        'userEmail' => $userEmail,
    ])

    @include('auth::emails.partials.security-note', [
        'body' => __('auth.verify_email_ignore'),
    ])

    @include('auth::emails.partials.url-fallback', [
        'url' => $verificationUrl ?? null,
    ])

    @include('auth::emails.partials.signature')
@endsection
