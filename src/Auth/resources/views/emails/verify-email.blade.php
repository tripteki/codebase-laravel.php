@extends('auth::layouts.mail')

@php
    $app = $appName ?? config('app.name');
@endphp

@section('title', $appName ?? config('app.name'))

@section('header_badge')
    {{ $appName ?? config('app.name') }}
@endsection

@section('content')
    <h1 class="mb-2" style="color: var(--brand-primary);">{{ __('auth.email_registration_confirmed') }}</h1>

    <p class="fw-bold mb-2">@if ($userName){{ __('auth.email_hi') }} {{ $userName }},@else{{ __('auth.hi_there') }}@endif</p>

    <p class="mb-4 text-muted small">{{ __('auth.email_thank_you_register') }}</p>

    @if ($userName !== null || $userEmail !== null)
        <div class="panel panel-account mb-4">
            <p class="fw-semibold panel-account-title">{{ __('auth.email_user_information') }}</p>
            <div class="small panel-account-body">
                @if ($userName !== null)
                    <p class="mb-2"><strong>{{ __('auth.full_name') }}</strong>: {{ $userName }}</p>
                @endif
                @if ($userEmail !== null)
                    <p class="mb-0"><strong>{{ __('auth.email_address') }}</strong>: <span class="pill">{{ $userEmail }}</span></p>
                @endif
            </div>
        </div>
    @endif

    <p class="mt-4 mb-0">
        {{ __('auth.thanks') }}<br>
        <strong>{{ $app }}</strong>.
    </p>
@endsection

@section('footer')
    &copy; {{ date('Y') }} {{ $app }}
@endsection
