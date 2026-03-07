@extends('layouts.app')

@section('content')
    <div class="auth-login-page relative min-h-screen flex flex-col lg:flex-row bg-gray-50 dark:bg-gray-950">
        <div class="absolute top-3 left-4 sm:left-6 z-20 flex items-center gap-2">
            @php
                $AddOnsHelper = App\Helpers\AddOnsHelper::class;
                $AddOnEnum = App\Enum\Event\AddOnEnum::class;
            @endphp
            @include('components.theme-toggle')
            @if (! hasTenant() || $AddOnsHelper::has($AddOnEnum::FEATURES_MULTI_LANGUAGE))
                @include('components.i18n-switcher', ['position' => 'bottom'])
            @endif
        </div>

        @yield('auth_right')
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/module/auth.css') }}">
    <style>
        .auth-login-page #theme-toggle { color: #ffffff !important; }
        .auth-login-page .i18n-trigger { color: #ffffff !important; background: none !important; }
    </style>
@endpush

@push('scripts-start')
    <script>
        (function () {
            var el = document.documentElement;
            el.setAttribute("data-default-theme", "dark");
            try {
                if (! window.localStorage.getItem("theme")) {
                    el.classList.add("dark");
                }
            } catch (e) {
                el.classList.add("dark");
            }
        })();
    </script>
@endpush

@push('scripts-end')
    <script src="{{ asset('js/module/auth.js') }}"></script>
    <script src="{{ asset('js/module/user.js') }}"></script>
@endpush
