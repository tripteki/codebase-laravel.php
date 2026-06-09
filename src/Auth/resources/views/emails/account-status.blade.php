@extends('auth::layouts.mail')

@php
    $isDeactivated = str_contains((string) $headingKey, 'deactivated');
@endphp

@section('title', __($headingKey))

@section('preheader')
    {{ __($lineKey) }}
@endsection

@section('header_badge')
    {{ $appName }}
@endsection

@section('content')
    @include('auth::emails.partials.hero', [
        'icon' => $isDeactivated ? '⏸' : '✓',
        'tone' => $isDeactivated ? 'warning' : 'success',
        'title' => __($headingKey),
        'subtitle' => __($lineKey),
    ])

    @include('auth::emails.partials.greeting', ['userName' => $userName])

    @include('auth::emails.partials.account-panel', [
        'userName' => $userName,
        'userEmail' => $userEmail,
    ])

    @if ($isDeactivated)
        @include('auth::emails.partials.security-note', [
            'title' => __('auth.account_status_support_title'),
            'body' => __('auth.account_status_support_body'),
        ])
    @endif

    @include('auth::emails.partials.signature')
@endsection
