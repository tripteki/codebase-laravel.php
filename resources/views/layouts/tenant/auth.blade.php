@extends('layouts.app')

@section('content')
    @php
        use App\Models\Setting;

        $isCentralAuthLayout = ! hasTenant();
        $authBrandDotted = implode(
            '.',
            array_map(
                static fn (string $c): string => mb_strtoupper($c, 'UTF-8'),
                mb_str_split((string) preg_replace('/\s+/u', '', config('app.name')), 1, 'UTF-8'),
            ),
        );
        $centralAuthSetting = static function (string $key): string {
            $v = Setting::query()->whereNull('tenant_id')->where('key', $key)->value('value');

            return trim((string) ($v ?? ''));
        };
        if ($isCentralAuthLayout) {
            $paneEventLive = $centralAuthSetting('CONTENT_AUTH_EVENT_LIVE') ?: __('auth.event_live');
            $paneInteractiveTech = $centralAuthSetting('CONTENT_AUTH_INTERACTIVE_TECH') ?: __('auth.interactive_tech');
            $paneEcosystem = $centralAuthSetting('CONTENT_AUTH_ECOSYSTEM') ?: __('auth.ecosystem');
            $paneBrandRaw = $centralAuthSetting('CONTENT_AUTH_BRAND_DESCRIPTION');
            $paneBrandDescription = $paneBrandRaw !== ''
                ? str_replace(':app', $authBrandDotted, $paneBrandRaw)
                : __('auth.brand_description', ['app' => $authBrandDotted]);
        }
    @endphp
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

        <div class="hidden lg:flex lg:w-1/2 items-center justify-center bg-white dark:bg-gray-900 px-6 xl:px-12 2xl:px-16">
            <div class="w-full max-w-[640px]">
                <h1 class="text-4xl xl:text-5xl 2xl:text-6xl font-extrabold leading-[1.12] mb-4 tracking-tight">
                    @if ($isCentralAuthLayout)
                        <span class="auth-text-animate-1 inline-block">{{ $paneEventLive }}</span><br>
                        <span class="auth-text-animate-2 inline-block">{{ $paneInteractiveTech }}</span><br>
                        <span class="auth-text-animate-3 inline-block">{{ $paneEcosystem }}</span>
                    @else
                        <span class="auth-text-animate-1 inline-block">{{ tenant_trans('auth.event_live') }}</span><br>
                        <span class="auth-text-animate-2 inline-block">{{ tenant_trans('auth.interactive_tech') }}</span><br>
                        <span class="auth-text-animate-3 inline-block">{{ tenant_trans('auth.ecosystem') }}</span>
                    @endif
                </h1>

                <p class="text-base xl:text-lg text-gray-600 dark:text-gray-300 mb-6 leading-relaxed max-w-[56ch]">
                    @if ($isCentralAuthLayout)
                        {{ $paneBrandDescription }}
                    @else
                        {{ tenant_trans('auth.brand_description', ['app' => $authBrandDotted]) }}
                    @endif
                </p>

                @isset($cta_route, $cta_label)
                    <a
                        href="{{ $cta_route }}"
                        class="inline-flex items-center justify-center rounded-full auth-cta px-4 py-2 text-sm font-semibold text-white"
                    >
                        {{ $cta_label }}
                    </a>
                @endisset
            </div>
        </div>

        @yield('auth_right')
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/module/auth.css') }}">
@endpush

@push('scripts-end')
    <script src="{{ asset('js/module/auth.js') }}"></script>
    <script src="{{ asset('js/module/user.js') }}"></script>
@endpush
