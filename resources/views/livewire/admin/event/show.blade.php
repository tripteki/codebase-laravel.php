<div class="min-h-screen flex flex-col bg-gray-100 lg:pl-64 dark:bg-gray-900" data-sidebar-content>
    @include('components.header')
    <main id="main-content" class="flex-1 px-4 py-6 lg:px-6">
        @php
            $TenantPermissionEnum = App\Enum\Tenant\PermissionEnum::class;
        @endphp
        <div class="mx-auto">
            <div class="mb-4">
                <nav class="flex mb-5" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 text-sm font-medium md:space-x-2">
                        <li class="inline-flex items-center">
                            <a href="{{ tenant_routes('admin.dashboard.index') }}"
                                class="inline-flex items-center text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white">
                                <svg class="w-4 h-4 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M10.707 2.293a1 1 0 0 0-1.414 0l-7 7a1 1 0 0 0 1.414 1.414L4 10.414V17a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-6.586l.293.293a1 1 0 0 0 1.414-1.414l-7-7z">
                                    </path>
                                </svg>
                                {{ __('common.home') }}
                            </a>
                        </li>
                        @can($TenantPermissionEnum::EVENT_VIEW->value)
                            <li>
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-gray-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M7.293 14.707a1 1 0 0 1 0-1.414L10.586 10 7.293 6.707a1 1 0 0 1 1.414-1.414l4 4a1 1 0 0 1 0 1.414l-4 4a1 1 0 0 1-1.414 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <a href="{{ tenant_routes('admin.tenants.events.index') }}"
                                        class="inline-flex items-center ml-1 text-gray-700 hover:text-primary-600 md:ml-2 dark:text-gray-300 dark:hover:text-white">
                                        <svg class="w-4 h-4 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd"
                                                d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        {{ __('module_event.module_title') }}
                                    </a>
                                </div>
                            </li>
                        @endcan
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 0 1 0-1.414L10.586 10 7.293 6.707a1 1 0 0 1 1.414-1.414l4 4a1 1 0 0 1 0 1.414l-4 4a1 1 0 0 1-1.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="inline-flex items-center ml-1 text-gray-400 md:ml-2 dark:text-gray-500"
                                    aria-current="page">
                                    <svg class="w-4 h-4 mr-2 shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ __('module_base.view') }}
                                </span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">
                    {{ __('module_event.show_title') }}</h1>
            </div>
            <div x-data="{ activeStep: 'information' }" class="bg-white rounded-lg shadow dark:bg-gray-800">
                <div class="flex justify-center w-full">
                    <ol
                        class="flex flex-wrap justify-center items-center w-full max-w-4xl p-4 text-sm font-medium text-center text-primary-600 dark:text-primary-500 sm:text-base sm:p-6 gap-x-3 gap-y-3 sm:gap-x-0 sm:gap-y-0">
                        <li
                            class="flex flex-none sm:flex-1 items-center after:content-[''] after:w-full after:h-1 after:border-b after:border-gray-200 after:border-1 after:hidden sm:after:inline-block after:mx-2 xl:after:mx-4 dark:after:border-gray-700">
                            <button type="button" @click="activeStep = 'information'"
                                class="flex items-center after:content-['/'] sm:after:hidden after:mx-2 after:text-gray-200 dark:after:text-gray-500">
                                <span
                                    class="flex items-center justify-center w-8 h-8 rounded-full bg-green-100 dark:bg-green-800 shrink-0 me-2">
                                    <svg class="w-4 h-4 text-green-600 dark:text-green-300" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                                <span class="inline-flex text-xs sm:text-sm whitespace-nowrap"
                                    :class="activeStep === 'information' ? 'text-primary-600 dark:text-primary-400' :
                                        'text-gray-500 dark:text-gray-400'">
                                    {{ __('module_event.step_information') }}
                                </span>
                            </button>
                        </li>
                        <li
                            class="flex flex-none sm:flex-1 items-center after:content-[''] after:w-full after:h-1 after:border-b after:border-gray-200 after:border-1 after:hidden sm:after:inline-block after:mx-2 xl:after:mx-4 dark:after:border-gray-700">
                            <button type="button" @click="activeStep = 'domain'"
                                class="flex items-center after:content-['/'] sm:after:hidden after:mx-2 after:text-gray-200 dark:after:text-gray-500">
                                <span
                                    class="flex items-center justify-center w-8 h-8 rounded-full bg-green-100 dark:bg-green-800 shrink-0 me-2">
                                    <svg class="w-4 h-4 text-green-600 dark:text-green-300" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                                <span class="inline-flex text-xs sm:text-sm whitespace-nowrap"
                                    :class="activeStep === 'domain' ? 'text-primary-600 dark:text-primary-400' :
                                        'text-gray-500 dark:text-gray-400'">
                                    {{ __('module_event.step_domain') }}
                                </span>
                            </button>
                        </li>
                        <li
                            class="flex flex-none sm:flex-1 items-center after:content-[''] after:w-full after:h-1 after:border-b after:border-gray-200 after:border-1 after:hidden sm:after:inline-block after:mx-2 xl:after:mx-4 dark:after:border-gray-700">
                            <button type="button" @click="activeStep = 'key-visual'"
                                class="flex items-center after:content-['/'] sm:after:hidden after:mx-2 after:text-gray-200 dark:after:text-gray-500">
                                <span
                                    class="flex items-center justify-center w-8 h-8 rounded-full bg-green-100 dark:bg-green-800 shrink-0 me-2">
                                    <svg class="w-4 h-4 text-green-600 dark:text-green-300" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                                <span class="inline-flex text-xs sm:text-sm whitespace-nowrap"
                                    :class="activeStep === 'key-visual' ? 'text-primary-600 dark:text-primary-400' :
                                        'text-gray-500 dark:text-gray-400'">
                                    {{ __('module_event.step_key_visual') }}
                                </span>
                            </button>
                        </li>
                        <li class="flex flex-none sm:flex-1 items-center">
                            <button type="button" @click="activeStep = 'addons'" class="flex items-center">
                                <span
                                    class="flex items-center justify-center w-8 h-8 rounded-full bg-green-100 dark:bg-green-800 shrink-0 me-2">
                                    <svg class="w-4 h-4 text-green-600 dark:text-green-300" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                                <span class="inline-flex text-xs sm:text-sm whitespace-nowrap"
                                    :class="activeStep === 'addons' ? 'text-primary-600 dark:text-primary-400' :
                                        'text-gray-500 dark:text-gray-400'">
                                    {{ __('module_event.step_add_ons') }}
                                </span>
                            </button>
                        </li>
                    </ol>
                </div>

                <div class="p-6 pt-0 space-y-8">
                    <section id="event-information" x-show="activeStep === 'information'">
                        <h2 class="mb-4 text-sm font-semibold text-gray-900 dark:text-white">
                            {{ __('module_event.step_information') }}</h2>
                        <div class="space-y-4">
                            <div>
                                <label
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_event.slug') }}</label>
                                <div
                                    class="bg-gray-50 text-gray-900 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:text-white">
                                    {{ $tenant->id ?: '-' }}</div>
                            </div>
                            <div>
                                <label
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_event.title') }}</label>
                                <div
                                    class="bg-gray-50 text-gray-900 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:text-white">
                                    {{ $tenant->getAttribute('title') ?: '-' }}</div>
                            </div>
                            <div>
                                <label
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_event.description') }}</label>
                                <div
                                    class="bg-gray-50 text-gray-900 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:text-white">
                                    {{ $tenant->getAttribute('description') ?: '-' }}</div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_event.start_date') }}</label>
                                    <div
                                        class="bg-gray-50 text-gray-900 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:text-white">
                                        {{ $tenant->getAttribute('event_start_date') ?: '-' }}</div>
                                </div>
                                <div>
                                    <label
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_event.start_time') }}</label>
                                    <div
                                        class="bg-gray-50 text-gray-900 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:text-white">
                                        {{ $tenant->getAttribute('event_start_time') ? \Carbon\Carbon::parse('today ' . $tenant->getAttribute('event_start_time'))->format('g:i A') : '-' }}
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_event.end_date') }}</label>
                                    <div
                                        class="bg-gray-50 text-gray-900 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:text-white">
                                        {{ $tenant->getAttribute('event_end_date') ?: '-' }}</div>
                                </div>
                                <div>
                                    <label
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_event.end_time') }}</label>
                                    <div
                                        class="bg-gray-50 text-gray-900 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:text-white">
                                        {{ $tenant->getAttribute('event_end_time') ? \Carbon\Carbon::parse('today ' . $tenant->getAttribute('event_end_time'))->format('g:i A') : '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section id="event-domain" x-show="activeStep === 'domain'">
                        <h2 class="mb-4 text-sm font-semibold text-gray-900 dark:text-white">
                            {{ __('module_event.step_domain') }}</h2>
                        @php
                            $centralDomains = config('tenancy.central_domains', []);
                            $host =
                                is_array($centralDomains) && isset($centralDomains[0])
                                    ? $centralDomains[0]
                                    : '';
                        @endphp
                        @if (config('tenancy.type') === 'path')
                            @php
                                $slug = (string) ($tenant->id ?? '');
                            @endphp
                            @if ($slug === '')
                                <span class="text-gray-500 dark:text-gray-400">-</span>
                            @else
                                @php
                                    $domainUrl = tenant_public_path_url($slug);
                                    $domainLabel = e(tenant_public_path_label($slug));
                                @endphp
                                <a href="{{ $domainUrl }}" target="_blank" rel="noopener noreferrer"
                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium badge-primary dark:badge-primary-dark">{{ $domainLabel }}</a>
                            @endif
                        @else
                            @if ($tenant->domains->isEmpty())
                                <span class="text-gray-500 dark:text-gray-400">-</span>
                            @else
                                <div class="flex flex-col gap-1">
                                    @foreach ($tenant->domains as $domain)
                                        @php
                                            $domainPart = trim((string) $domain->domain);
                                        @endphp
                                        @if ($domainPart !== '')
                                            @php
                                                $displayHost = str_contains($domainPart, '.')
                                                    ? $domainPart
                                                    : $domainPart . '.' . $host;
                                                $domainUrl = tenant_public_domain_url($domainPart, $host);
                                            @endphp
                                            <a href="{{ $domainUrl }}" target="_blank" rel="noopener noreferrer"
                                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium badge-primary dark:badge-primary-dark w-fit">{{ $displayHost }}</a>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        @endif
                    </section>

                    <section id="event-key-visual" x-show="activeStep === 'key-visual'">
                        <h2 class="mb-4 text-sm font-semibold text-gray-900 dark:text-white text-center">
                            {{ __('module_event.step_key_visual') }}</h2>
                        @php
                            $SettingHelper = \App\Helpers\SettingHelper::class;
                            $defaultPrimaryColor = ($p = $SettingHelper::get('COLOR_PRIMARY')) !== null ? trim((string) $p) : null;
                            $defaultSecondaryColor = ($s = $SettingHelper::get('COLOR_SECONDARY')) !== null ? trim((string) $s) : null;
                            $defaultTertiaryColor = ($t = $SettingHelper::get('COLOR_TERTIARY')) !== null ? trim((string) $t) : null;

                            $primaryColor = (string) ($tenant->getAttribute('primary_color') ?? $defaultPrimaryColor);
                            $secondaryColor =
                                (string) ($tenant->getAttribute('secondary_color') ?? $defaultSecondaryColor);
                            $tertiaryColor =
                                (string) ($tenant->getAttribute('tertiary_color') ?? $defaultTertiaryColor);
                        @endphp

                        <div class="w-full flex flex-col items-center">
                            <div class="w-full max-w-3xl mx-auto space-y-8">
                                <div class="space-y-4">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ __('module_event.key_visual_assets') }}
                                    </div>
                                    @php
                                        $defaultFaviconIcoPath = 'favicon.ico';
                                        $defaultFaviconPngPath = 'asset/favicon.png';
                                        $defaultLogoPngPath = 'asset/logo.png';
                                        $defaultBrandLightPath = 'asset/brand-light.png';
                                        $defaultBrandDarkPath = 'asset/brand-dark.png';

                                        $faviconIcoVal = $tenant->getAttribute('favicon_ico');
                                        $faviconPngVal = $tenant->getAttribute('favicon_png');
                                        $iconVal = $tenant->getAttribute('icon');
                                        $brandLightVal = $tenant->getAttribute('brand_light');
                                        $brandDarkVal = $tenant->getAttribute('brand_dark');

                                        $faviconIcoSrc = $faviconIcoVal
                                            ? asset('storage/' . $faviconIcoVal)
                                            : asset($defaultFaviconIcoPath);
                                        $faviconPngSrc = $faviconPngVal
                                            ? asset('storage/' . $faviconPngVal)
                                            : asset($defaultFaviconPngPath);
                                        $logoPngSrc = $iconVal
                                            ? asset('storage/' . $iconVal)
                                            : asset($defaultLogoPngPath);
                                        $brandLightSrc = $brandLightVal
                                            ? asset('storage/' . $brandLightVal)
                                            : asset($defaultBrandLightPath);
                                        $brandDarkSrc = $brandDarkVal
                                            ? asset('storage/' . $brandDarkVal)
                                            : asset($defaultBrandDarkPath);

                                        $faviconIcoPathText = $faviconIcoVal
                                            ? 'storage/' . $faviconIcoVal
                                            : $defaultFaviconIcoPath;
                                        $faviconPngPathText = $faviconPngVal
                                            ? 'storage/' . $faviconPngVal
                                            : $defaultFaviconPngPath;
                                        $logoPngPathText = $iconVal ? 'storage/' . $iconVal : $defaultLogoPngPath;
                                        $brandLightPathText = $brandLightVal
                                            ? 'storage/' . $brandLightVal
                                            : $defaultBrandLightPath;
                                        $brandDarkPathText = $brandDarkVal
                                            ? 'storage/' . $brandDarkVal
                                            : $defaultBrandDarkPath;
                                    @endphp

                                    <div class="space-y-5">
                                        {{-- 128x128 favicon.ico --}}
                                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                                            <div
                                                class="w-16 h-16 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 flex items-center justify-center text-[10px] font-medium text-gray-500 dark:text-gray-300">
                                                ICO
                                            </div>
                                            <div class="flex-1 space-y-1">
                                                <div class="text-xs font-medium text-gray-900 dark:text-gray-100">
                                                    128x128 favicon.ico
                                                </div>
                                                <div class="text-[11px] text-gray-500 dark:text-gray-400">
                                                    Path: <code>{{ $faviconIcoPathText }}</code>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- 128x128 favicon.png --}}
                                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                                            <div
                                                class="w-16 h-16 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 flex items-center justify-center">
                                                <img src="{{ $faviconPngSrc }}" alt="128x128 favicon.png"
                                                    class="w-full h-full object-contain" />
                                            </div>
                                            <div class="flex-1 space-y-1">
                                                <div class="text-xs font-medium text-gray-900 dark:text-gray-100">
                                                    128x128 favicon.png
                                                </div>
                                                <div class="text-[11px] text-gray-500 dark:text-gray-400">
                                                    Path: <code>{{ $faviconPngPathText }}</code>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- 512x512 logo.png --}}
                                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                                            <div
                                                class="w-20 h-20 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 flex items-center justify-center">
                                                <img src="{{ $logoPngSrc }}" alt="512x512 logo.png"
                                                    class="w-full h-full object-contain" />
                                            </div>
                                            <div class="flex-1 space-y-1">
                                                <div class="text-xs font-medium text-gray-900 dark:text-gray-100">
                                                    512x512 logo.png
                                                </div>
                                                <div class="text-[11px] text-gray-500 dark:text-gray-400">
                                                    Path: <code>{{ $logoPngPathText }}</code>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- 3200x733 brand-light --}}
                                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                                            <div
                                                class="w-full sm:w-64 h-14 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 flex items-center justify-center">
                                                <img src="{{ $brandLightSrc }}" alt="3200x733 brand-light"
                                                    class="w-full h-full object-contain" />
                                            </div>
                                            <div class="flex-1 space-y-1">
                                                <div class="text-xs font-medium text-gray-900 dark:text-gray-100">
                                                    3200x733 brand-light
                                                </div>
                                                <div class="text-[11px] text-gray-500 dark:text-gray-400">
                                                    Path: <code>{{ $brandLightPathText }}</code>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- 3200x733 brand-dark --}}
                                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                                            <div
                                                class="w-full sm:w-64 h-14 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 flex items-center justify-center">
                                                <img src="{{ $brandDarkSrc }}" alt="3200x733 brand-dark"
                                                    class="w-full h-full object-contain" />
                                            </div>
                                            <div class="flex-1 space-y-1">
                                                <div class="text-xs font-medium text-gray-900 dark:text-gray-100">
                                                    3200x733 brand-dark
                                                </div>
                                                <div class="text-[11px] text-gray-500 dark:text-gray-400">
                                                    Path: <code>{{ $brandDarkPathText }}</code>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="border-t border-gray-200 dark:border-gray-700 pt-6 space-y-4">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ __('module_event.key_visual_colors') }}
                                    </div>
                                    <div class="grid gap-6 md:grid-cols-3">
                                        <div class="space-y-2">
                                            <label class="block text-sm font-medium text-gray-900 dark:text-white">
                                                {{ __('module_event.primary_color') }}
                                            </label>
                                            <div class="flex items-center gap-3">
                                                <div class="h-10 w-10 rounded-md border border-gray-300 dark:border-gray-600"
                                                    style="background-color: {{ $primaryColor }};"></div>
                                                <code
                                                    class="text-xs text-gray-700 dark:text-gray-200">{{ $primaryColor }}</code>
                                            </div>
                                        </div>

                                        <div class="space-y-2">
                                            <label class="block text-sm font-medium text-gray-900 dark:text-white">
                                                {{ __('module_event.secondary_color') }}
                                            </label>
                                            <div class="flex items-center gap-3">
                                                <div class="h-10 w-10 rounded-md border border-gray-300 dark:border-gray-600"
                                                    style="background-color: {{ $secondaryColor }};"></div>
                                                <code
                                                    class="text-xs text-gray-700 dark:text-gray-200">{{ $secondaryColor }}</code>
                                            </div>
                                        </div>

                                        <div class="space-y-2">
                                            <label class="block text-sm font-medium text-gray-900 dark:text-white">
                                                {{ __('module_event.tertiary_color') }}
                                            </label>
                                            <div class="flex items-center gap-3">
                                                <div class="h-10 w-10 rounded-md border border-gray-300 dark:border-gray-600"
                                                    style="background-color: {{ $tertiaryColor }};"></div>
                                                <code
                                                    class="text-xs text-gray-700 dark:text-gray-200">{{ $tertiaryColor }}</code>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <div class="text-xs font-medium text-gray-900 dark:text-white">
                                            {{ __('module_event.preview_gradient') }}
                                        </div>
                                        <div class="h-12 w-full rounded-lg border border-gray-200 dark:border-gray-700"
                                            style="background: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $secondaryColor }} 50%, {{ $tertiaryColor }} 100%);">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section id="event-addons" x-show="activeStep === 'addons'">
                        @php
                            $AddOnsHelper = App\Helpers\AddOnsHelper::class;
                            $AddOnEnum = App\Enum\Event\AddOnEnum::class;

                            $enabledFeatures = $AddOnsHelper::enabledFeatureCases($tenant);
                            $enabledModuleGroups = $AddOnsHelper::enabledModuleCasesGroupedByCategory($tenant);
                            $addOnsConfig = $tenant->getAttribute('add_ons_config');
                            $addOnsConfig = is_array($addOnsConfig) ? $addOnsConfig : [];
                        @endphp
                        <h2 class="mb-4 text-sm font-semibold text-gray-900 dark:text-white">
                            {{ __('module_event.step_add_ons') }}</h2>
                        <div class="space-y-6">
                            @if (count($enabledFeatures) > 0)
                                <div class="space-y-2">
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ __('module_event.add_ons_features') }}</h3>
                                    <ul class="space-y-1.5 text-sm text-gray-700 dark:text-gray-300">
                                        @foreach ($enabledFeatures as $feature)
                                            @php
                                                $rawConfig = $addOnsConfig[$feature->value] ?? null;
                                                $rawConfig = is_array($rawConfig) ? $rawConfig : [];
                                                $displayConfig = $feature->buildFeatureDisplayConfig($rawConfig);
                                            @endphp
                                            <li>
                                                <div>{{ __($feature->labelKey()) }}</div>
                                                @if ($feature->hasFeatureConfiguration() && !empty($displayConfig))
                                                    <div
                                                        class="ml-2 pl-2 border-l border-gray-200 dark:border-gray-700 text-[11px]">
                                                        <span
                                                            class="font-medium text-gray-500 dark:text-gray-400">{{ __('module_event.feature_config') }}</span>
                                                        <ul class="mt-0.5 space-y-0.5 list-none pl-0">
                                                            @foreach ($displayConfig as $confKey => $confValue)
                                                                <li
                                                                    class="flex gap-x-2 items-baseline font-mono text-[11px]">
                                                                    <span
                                                                        class="text-gray-500 dark:text-gray-400 shrink-0 w-44">{{ $confKey }}:</span>
                                                                    <span
                                                                        class="text-gray-900 dark:text-gray-100 min-w-0">{{ $confValue === null || $confValue === '' ? '-' : $confValue }}</span>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if (count($enabledModuleGroups) > 0)
                                <div
                                    class="{{ count($enabledFeatures) > 0 ? 'border-t border-gray-200 dark:border-gray-700 pt-6' : '' }} space-y-3">
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ __('module_event.add_ons_modules') }}</h3>
                                    @foreach ($enabledModuleGroups as $categoryKey => $modules)
                                        <div class="space-y-3">
                                            <h4 class="text-xs font-medium text-gray-600 dark:text-gray-400">
                                                {{ __($AddOnEnum::categoryLabelKeyFor($categoryKey)) }}
                                            </h4>
                                            <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300 ml-3">
                                                @foreach ($modules as $module)
                                                    <li>{{ __($module->labelKey()) }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            @if (count($enabledFeatures) === 0 && count($enabledModuleGroups) === 0)
                                <p class="text-sm text-gray-500 dark:text-gray-400">-</p>
                            @endif
                        </div>
                    </section>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                        @can($TenantPermissionEnum::EVENT_VIEW->value)
                            <a href="{{ tenant_routes('admin.tenants.events.index') }}"
                                class="inline-flex items-center px-2 py-1 text-xs font-medium text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-700">{{ __('module_base.back_to_list') }}</a>
                        @else
                            <span></span>
                        @endcan
                        @can($TenantPermissionEnum::EVENT_UPDATE->value)
                            <a href="{{ tenant_routes('admin.tenants.events.edit', $tenant) }}"
                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-center text-white rounded-lg btn-tertiary">{{ __('module_event.edit_event') }}</a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </main>
    @include('components.footer')
</div>
