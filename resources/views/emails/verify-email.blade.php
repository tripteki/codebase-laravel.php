@extends('layouts.mail')

@php
    $SettingHelper = App\Helpers\SettingHelper::class;
    $app = $appName ?? config('app.name');
    $allRightsReservedFooter = trim((string) $SettingHelper::get('ALL_RIGHTS_RESERVED', ''));
@endphp

@section('title', $appName ?? config('app.name'))

@section('header_badge')
    @if (! empty(trim((string) ($eventDescription ?? ''))))
        {{ trim($eventDescription) }}
    @else
        {{ $appName ?? config('app.name') }}
    @endif
@endsection

@section('content')
    @php
        $hasEventDates = $eventStart !== null || $eventEnd !== null;
        $hasEventDescription = ! empty(trim((string) ($eventDescription ?? '')));
    @endphp

    <h1 class="mb-2" style="color: var(--tenant-primary);">{{ __('auth.email_registration_confirmed') }}</h1>

    <p class="fw-bold mb-2">@if ($userName){{ __('auth.email_hi') }} {{ $userName }},@else{{ __('auth.hi_there') }}@endif</p>

    @if ($hasEventDates)
        <p class="mb-4" style="color:#334155;">
            {{ __('auth.email_see_you_dates') }}
        </p>
    @endif

    <p class="mb-4 text-muted small">{{ __('auth.email_thank_you_register') }}</p>

    @if ($hasEventDates || $hasEventDescription)
        <div class="panel panel-account mb-4">
            <p class="fw-semibold panel-account-title">{{ __('auth.email_event_information') }}</p>
            <div class="small panel-account-body">
                @if ($hasEventDates)
                    @if ($eventStart !== null)
                        <p class="{{ $eventEnd !== null ? 'mb-2' : 'mb-0' }}">
                            <strong>{{ __('module_event.start_date') }}</strong>: {{ $eventStart }}
                        </p>
                    @endif
                    @if ($eventEnd !== null)
                        <p class="mb-0">
                            <strong>{{ __('module_event.end_date') }}</strong>: {{ $eventEnd }}
                        </p>
                    @endif
                @elseif ($hasEventDescription)
                    <p class="mb-0 whitespace-pre-wrap">{{ trim($eventDescription) }}</p>
                @endif
            </div>
        </div>
    @endif

    @if ($userName !== null || $userEmail !== null)
        <div class="panel panel-account mb-4">
            <p class="fw-semibold panel-account-title">{{ __('auth.email_user_information') }}</p>
            <div class="small panel-account-body">
                @if ($userName !== null)
                    <p class="mb-2"><strong>{{ __('module_user.full_name') }}</strong>: {{ $userName }}</p>
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
    &copy; {{ date('Y') }} {{ $app }}@if ($allRightsReservedFooter !== '')
        . {{ $allRightsReservedFooter }}
    @endif
@endsection
