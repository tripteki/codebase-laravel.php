@extends('auth::layouts.mail')

@section('title', __($headingKey))

@section('content')
    <h1 class="mb-2" style="color: var(--brand-primary);">{{ __($headingKey) }}</h1>

    <p class="fw-bold mb-2">@if ($userName){{ __('auth.email_hi') }} {{ $userName }},@else{{ __('auth.hi_there') }}@endif</p>

    <p class="mb-4 text-muted small">{{ __($lineKey) }}</p>

    <p class="mt-4 mb-0">
        {{ __('auth.thanks') }}<br>
        <strong>{{ config('app.name') }}</strong>.
    </p>
@endsection

@section('footer')
    &copy; {{ date('Y') }} {{ config('app.name') }}
@endsection
