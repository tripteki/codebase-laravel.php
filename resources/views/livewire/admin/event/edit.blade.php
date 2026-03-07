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
                                    <svg class="w-4 h-4 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M13.586 3.586a2 2 0 112.828 2.828l-.828.828-2.828-2.828.828-.828zM11.172 6L5 12.172V15h2.828l6.172-6.172-2.828-2.828z">
                                        </path>
                                    </svg>
                                    {{ __('module_base.edit') }}
                                </span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">
                    {{ __('module_event.edit_event') }}</h1>
            </div>

            <div class="bg-white rounded-lg shadow dark:bg-gray-800">
                <div class="flex justify-center w-full">
                    <ol
                        class="flex flex-wrap justify-center items-center w-full max-w-4xl p-4 text-sm font-medium text-center text-gray-500 sm:text-base sm:p-6 gap-x-3 gap-y-3 sm:gap-x-0 sm:gap-y-0">
                        <li
                            class="flex flex-none sm:flex-1 items-center {{ $step >= 1 ? 'text-primary-600 dark:text-primary-500' : '' }} after:content-[''] after:w-full after:h-1 after:border-b after:border-gray-200 after:border-1 after:hidden sm:after:inline-block after:mx-2 xl:after:mx-4 dark:after:border-gray-700">
                            <button type="button" wire:click="$set('step', 1)"
                                class="flex items-center after:content-['/'] sm:after:hidden after:mx-2 after:text-gray-200 dark:after:text-gray-500">
                                @if ($step > 1)
                                    <span
                                        class="flex items-center justify-center w-8 h-8 rounded-full bg-green-100 dark:bg-green-800 shrink-0 me-2">
                                        <svg class="w-4 h-4 text-green-600 dark:text-green-300" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                @else
                                    <span
                                        class="flex items-center justify-center w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900 shrink-0 me-2">1</span>
                                @endif
                                <span
                                    class="inline-flex text-xs sm:text-sm whitespace-nowrap {{ $step === 1 ? 'text-primary-600 dark:text-primary-500' : 'text-gray-500 dark:text-gray-400' }}">{{ __('module_event.step_information') }}</span>
                            </button>
                        </li>
                        @if (config('tenancy.type') !== 'path')
                            <li
                                class="flex flex-none sm:flex-1 items-center {{ $step >= 2 ? 'text-primary-600 dark:text-primary-500' : '' }} after:content-[''] after:w-full after:h-1 after:border-b after:border-gray-200 after:border-1 after:hidden sm:after:inline-block after:mx-2 xl:after:mx-4 dark:after:border-gray-700">
                                <button type="button" wire:click="$set('step', 2)"
                                    class="flex items-center after:content-['/'] sm:after:hidden after:mx-2 after:text-gray-200 dark:after:text-gray-500">
                                    @if ($step > 2)
                                        <span
                                            class="flex items-center justify-center w-8 h-8 rounded-full bg-green-100 dark:bg-green-800 shrink-0 me-2">
                                            <svg class="w-4 h-4 text-green-600 dark:text-green-300" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    @else
                                        <span
                                            class="flex items-center justify-center w-8 h-8 rounded-full {{ $step >= 2 ? 'bg-primary-100 dark:bg-primary-900' : 'bg-gray-100 dark:bg-gray-700' }} shrink-0 me-2">2</span>
                                    @endif
                                    <span
                                        class="inline-flex text-xs sm:text-sm whitespace-nowrap {{ $step === 2 ? 'text-primary-600 dark:text-primary-500' : 'text-gray-500 dark:text-gray-400' }}">{{ __('module_event.step_domain') }}</span>
                                </button>
                            </li>
                        @endif
                        <li
                            class="flex flex-none sm:flex-1 items-center {{ $step >= 3 ? 'text-primary-600 dark:text-primary-500' : '' }} after:content-[''] after:w-full after:h-1 after:border-b after:border-gray-200 after:border-1 after:hidden sm:after:inline-block after:mx-2 xl:after:mx-4 dark:after:border-gray-700">
                            <button type="button" wire:click="$set('step', 3)"
                                class="flex items-center after:content-['/'] sm:after:hidden after:mx-2 after:text-gray-200 dark:after:text-gray-500">
                                @if ($step > 3)
                                    <span
                                        class="flex items-center justify-center w-8 h-8 rounded-full bg-green-100 dark:bg-green-800 shrink-0 me-2">
                                        <svg class="w-4 h-4 text-green-600 dark:text-green-300" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                @else
                                    <span
                                        class="flex items-center justify-center w-8 h-8 rounded-full {{ $step >= 3 ? 'bg-primary-100 dark:bg-primary-900' : 'bg-gray-100 dark:bg-gray-700' }} shrink-0 me-2">{{ config('tenancy.type') === 'path' ? '2' : '3' }}</span>
                                @endif
                                <span
                                    class="inline-flex text-xs sm:text-sm whitespace-nowrap {{ $step === 3 ? 'text-primary-600 dark:text-primary-500' : 'text-gray-500 dark:text-gray-400' }}">{{ __('module_event.step_key_visual') }}</span>
                            </button>
                        </li>
                        <li
                            class="flex flex-none sm:flex-1 items-center {{ $step >= 4 ? 'text-primary-600 dark:text-primary-500' : '' }}">
                            <button type="button" wire:click="$set('step', 4)" class="flex items-center">
                                <span
                                    class="flex items-center justify-center w-8 h-8 rounded-full {{ $step >= 4 ? 'bg-primary-100 dark:bg-primary-900' : 'bg-gray-100 dark:bg-gray-700' }} shrink-0 me-2">{{ config('tenancy.type') === 'path' ? '3' : '4' }}</span>
                                <span
                                    class="inline-flex text-xs sm:text-sm whitespace-nowrap {{ $step === 4 ? 'text-primary-600 dark:text-primary-500' : 'text-gray-500 dark:text-gray-400' }}">{{ __('module_event.step_add_ons') }}</span>
                            </button>
                        </li>
                    </ol>
                </div>

                <form wire:submit.prevent="save" class="p-6 pt-0 space-y-6">
                    @if ($step === 1)
                        <div class="space-y-6">
                            <div>
                                <label for="slug"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_event.slug') }}</label>
                                <input type="text" id="slug" wire:model.defer="slug"
                                    placeholder="{{ __('module_event.slug_placeholder') }}"
                                    class="input-primary shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg  block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                                @error('slug')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="title"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_event.title') }}</label>
                                <textarea id="title" wire:model.defer="title" rows="3"
                                    placeholder="{{ __('module_event.title_placeholder') }}"
                                    class="input-primary shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg  block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"></textarea>
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="description"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_event.description') }}</label>
                                <textarea id="description" wire:model.defer="description" rows="5"
                                    placeholder="{{ __('module_event.description_placeholder') }}"
                                    class="input-primary shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg  block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"></textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-4">
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <label for="event-start-date"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                            {{ __('module_event.start_date') }}
                                        </label>
                                        @include('components.admin.date', [
                                            'id' => 'event-start-date',
                                            'wireModel' => 'eventStartDate',
                                            'value' => $eventStartDate ?? '',
                                            'minDate' => '',
                                            'maxDate' => $eventEndDate ?? '',
                                        ])
                                        @error('eventStartDate')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="event-start-time"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                            {{ __('module_event.start_time') }}
                                        </label>
                                        @include('components.admin.time', [
                                            'id' => 'event-start-time',
                                            'wireModel' => 'eventStartTime',
                                            'value' => $eventStartTime ?? null,
                                            'defaultHour' => '09',
                                            'defaultMinute' => '00',
                                            'defaultPeriod' => 'AM',
                                        ])
                                    </div>
                                </div>

                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <label for="event-end-date"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                            {{ __('module_event.end_date') }}
                                        </label>
                                        @if ($eventStartDate)
                                            <p class="mb-1 text-xs text-gray-500 dark:text-gray-400">
                                                {{ __('module_event.date_range_hint_from', ['min' => $eventStartDate]) }}
                                            </p>
                                        @endif
                                        @include('components.admin.date', [
                                            'id' => 'event-end-date',
                                            'wireModel' => 'eventEndDate',
                                            'value' => $eventEndDate ?? '',
                                            'minDate' => $eventStartDate ?? '',
                                            'maxDate' => '',
                                        ])
                                        @error('eventEndDate')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="event-end-time"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                            {{ __('module_event.end_time') }}
                                        </label>
                                        @include('components.admin.time', [
                                            'id' => 'event-end-time',
                                            'wireModel' => 'eventEndTime',
                                            'value' => $eventEndTime ?? null,
                                            'defaultHour' => '05',
                                            'defaultMinute' => '00',
                                            'defaultPeriod' => 'PM',
                                        ])
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (config('tenancy.type') !== 'path' && $step === 2)
                        <div>
                            <label
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_event.domains') }}</label>
                            <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">
                                {{ __('module_event.domain_required') }}</p>
                            @foreach ($domains as $index => $d)
                                <div class="flex gap-2 mb-2 items-center" wire:key="domain-edit-{{ $index }}">
                                    <input type="hidden" wire:model.defer="domains.{{ $index }}.id">
                                    <input type="text" wire:model.defer="domains.{{ $index }}.domain"
                                        placeholder="{{ __('module_event.domain_placeholder') }}"
                                        class="input-primary shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg  block flex-1 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                                    @if (count($domains) > 1)
                                        <button type="button" wire:click="removeDomain({{ $index }})"
                                            class="w-8 h-8 flex items-center justify-center text-sm font-medium text-white bg-[hsl(0,84.2%,60.2%)] rounded-full hover:bg-[hsl(0,84.2%,55%)] focus:ring-4 focus:ring-[hsl(0,84.2%,60.2%)]/30 dark:bg-[hsl(0,62.8%,30.6%)] dark:hover:bg-[hsl(0,62.8%,35%)] dark:focus:ring-[hsl(0,62.8%,30.6%)]/30 shrink-0"
                                            title="{{ __('module_base.delete') }}">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                            @error('domains')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            @error('domains.0.domain')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <button type="button" wire:click="addDomain"
                                class="mt-2 inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                {{ __('module_event.domain') }}
                            </button>
                        </div>
                    @endif

                    @if ($step === 3)
                        <div class="w-full flex flex-col items-center">
                            <div class="w-full max-w-3xl mx-auto space-y-8">
                                <div class="space-y-4">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white text-center">
                                        {{ __('module_event.key_visual_assets') }}
                                    </div>

                                    @php
                                        $defaultFaviconIcoPath = 'favicon.ico';
                                        $defaultFaviconPngPath = 'asset/favicon.png';
                                        $defaultLogoPngPath = 'asset/logo.png';
                                        $defaultBrandLightPath = 'asset/brand-light.png';
                                        $defaultBrandDarkPath = 'asset/brand-dark.png';
                                    @endphp

                                    {{-- 128x128 favicon.ico --}}
                                    @php
                                        $faviconIcoPathText = $faviconIcoUrl
                                            ? 'storage/' . $faviconIcoUrl
                                            : $defaultFaviconIcoPath;
                                    @endphp
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
                                            <input type="file" id="favicon-ico" wire:model.live="faviconIco"
                                                accept=".ico,image/x-icon"
                                                class="input-primary mt-1 block w-full text-xs text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600" />
                                            @error('faviconIco')
                                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}
                                                </p>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- 128x128 favicon.png --}}
                                    @php
                                        $faviconPngSrc = $faviconPng
                                            ? $faviconPng->temporaryUrl()
                                            : ($faviconPngUrl
                                                ? asset('storage/' . $faviconPngUrl)
                                                : asset($defaultFaviconPngPath));
                                        $faviconPngPathText = $faviconPngUrl
                                            ? 'storage/' . $faviconPngUrl
                                            : $defaultFaviconPngPath;
                                    @endphp
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
                                            <input type="file" id="favicon-png" wire:model.live="faviconPng"
                                                accept="image/png"
                                                class="input-primary mt-1 block w-full text-xs text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600" />
                                            @error('faviconPng')
                                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}
                                                </p>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- 512x512 logo.png (main app icon) --}}
                                    @php
                                        $logoPngSrc = $icon
                                            ? $icon->temporaryUrl()
                                            : ($iconUrl
                                                ? asset('storage/' . $iconUrl)
                                                : asset($defaultLogoPngPath));
                                        $logoPngPathText = $iconUrl ? 'storage/' . $iconUrl : $defaultLogoPngPath;
                                    @endphp
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
                                            <input type="file" id="icon" wire:model.live="icon"
                                                accept="image/*"
                                                class="input-primary mt-1 block w-full text-xs text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600" />
                                            @error('icon')
                                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}
                                                </p>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- 3200x733 brand-light --}}
                                    @php
                                        $brandLightSrc = $brandLight
                                            ? $brandLight->temporaryUrl()
                                            : ($brandLightUrl
                                                ? asset('storage/' . $brandLightUrl)
                                                : asset($defaultBrandLightPath));
                                        $brandLightPathText = $brandLightUrl
                                            ? 'storage/' . $brandLightUrl
                                            : $defaultBrandLightPath;
                                    @endphp
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
                                            <input type="file" id="brand-light" wire:model.live="brandLight"
                                                accept="image/*"
                                                class="input-primary mt-1 block w-full text-xs text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600" />
                                            @error('brandLight')
                                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}
                                                </p>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- 3200x733 brand-dark --}}
                                    @php
                                        $brandDarkSrc = $brandDark
                                            ? $brandDark->temporaryUrl()
                                            : ($brandDarkUrl
                                                ? asset('storage/' . $brandDarkUrl)
                                                : asset($defaultBrandDarkPath));
                                        $brandDarkPathText = $brandDarkUrl
                                            ? 'storage/' . $brandDarkUrl
                                            : $defaultBrandDarkPath;
                                    @endphp
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
                                            <input type="file" id="brand-dark" wire:model.live="brandDark"
                                                accept="image/*"
                                                class="input-primary mt-1 block w-full text-xs text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600" />
                                            @error('brandDark')
                                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}
                                                </p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="border-t border-gray-200 dark:border-gray-700 pt-6 space-y-4">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white text-center">
                                        {{ __('module_event.key_visual_colors') }}
                                    </div>

                                    <div class="grid gap-6 md:grid-cols-3">
                                        <div class="space-y-2">
                                            <label class="block text-sm font-medium text-gray-900 dark:text-white">
                                                {{ __('module_event.primary_color') }}
                                            </label>
                                            <div class="flex items-center gap-3">
                                                <input type="color" wire:model.defer="primaryColor"
                                                    class="h-10 w-10 rounded-md border border-gray-300 dark:border-gray-600 bg-transparent p-0 cursor-pointer">
                                                <input type="text" wire:model.defer="primaryColor"
                                                    class="flex-1 block w-full p-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg  dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                                                    placeholder="{{ __('module_event.primary_color_placeholder') }}">
                                            </div>
                                        </div>

                                        <div class="space-y-2">
                                            <label class="block text-sm font-medium text-gray-900 dark:text-white">
                                                {{ __('module_event.secondary_color') }}
                                            </label>
                                            <div class="flex items-center gap-3">
                                                <input type="color" wire:model.defer="secondaryColor"
                                                    class="h-10 w-10 rounded-md border border-gray-300 dark:border-gray-600 bg-transparent p-0 cursor-pointer">
                                                <input type="text" wire:model.defer="secondaryColor"
                                                    class="flex-1 block w-full p-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg  dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                                                    placeholder="{{ __('module_event.secondary_color_placeholder') }}">
                                            </div>
                                        </div>

                                        <div class="space-y-2">
                                            <label class="block text-sm font-medium text-gray-900 dark:text-white">
                                                {{ __('module_event.tertiary_color') }}
                                            </label>
                                            <div class="flex items-center gap-3">
                                                <input type="color" wire:model.defer="tertiaryColor"
                                                    class="h-10 w-10 rounded-md border border-gray-300 dark:border-gray-600 bg-transparent p-0 cursor-pointer">
                                                <input type="text" wire:model.defer="tertiaryColor"
                                                    class="flex-1 block w-full p-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg  dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                                                    placeholder="{{ __('module_event.tertiary_color_placeholder') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <div class="text-xs font-medium text-gray-900 dark:text-white">
                                            {{ __('module_event.preview_gradient') }}
                                        </div>
                                        <div class="h-12 w-full rounded-lg border border-gray-200 dark:border-gray-700"
                                            style="background: linear-gradient(135deg, {{ $primaryColor ?? \App\Helpers\SettingHelper::get('COLOR_PRIMARY') }} 0%, {{ $secondaryColor ?? \App\Helpers\SettingHelper::get('COLOR_SECONDARY') }} 50%, {{ $tertiaryColor ?? \App\Helpers\SettingHelper::get('COLOR_TERTIARY') }} 100%);">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($step === 4)
                        @php
                            $addOnIdSuffix = '-edit';
                            $AddOnEnum = App\Enum\Event\AddOnEnum::class;
                            $featureCases = $AddOnEnum::features();
                            $moduleGroups = $AddOnEnum::modulesGroupedByCategory();
                        @endphp
                        <div class="space-y-6">
                            <div class="space-y-3">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ __('module_event.add_ons_features') }}</h3>
                                <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                    @foreach ($featureCases as $feature)
                                        @php
                                            $id = 'addon-checklist-' . $feature->inputIdPart() . $addOnIdSuffix;
                                            $checked = $this->hasAddOnFeature($feature->value);
                                            $disabled = $feature->isDefault();
                                        @endphp
                                        <li class="space-y-2"
                                            wire:key="addon-feature-{{ $feature->inputIdPart() }} {{ $addOnIdSuffix }}">
                                            <div class="flex flex-col md:flex-row md:items-start gap-3">
                                                <div class="flex items-center gap-2 md:w-64">
                                                    <input type="checkbox" id="{{ $id }}"
                                                        {{ $checked ? 'checked' : '' }}
                                                        {{ $disabled ? 'disabled' : '' }}
                                                        @if (!$disabled) wire:click="toggleAddOnFeature('{{ $feature->value }}')" @endif
                                                        class="checkbox-primary h-4 w-4 rounded border-gray-300 bg-gray-100 focus:ring-2 focus:ring-offset-0 dark:border-gray-600 dark:bg-gray-700 {{ $disabled ? 'opacity-70 cursor-not-allowed' : '' }}">
                                                    <label for="{{ $id }}"
                                                        class="{{ $disabled ? 'text-gray-500 dark:text-gray-400' : '' }}">
                                                        {{ __($feature->labelKey()) }}
                                                    </label>
                                                </div>

                                                @if ($feature->hasFeatureConfiguration())
                                                    <div class="flex-1">
                                                        <details @if ($checked) open @endif
                                                            class="{{ $checked ? '' : 'opacity-60' }}"
                                                            @if (!$checked) aria-disabled="true" @endif>
                                                            <summary
                                                                class="cursor-pointer select-none text-xs font-medium text-gray-600 dark:text-gray-300 hover:underline">
                                                                {{ __('module_event.feature_config') }}
                                                            </summary>
                                                            @if ($checked)
                                                                <div class="mt-2 space-y-2">
                                                                    @if ($hintKey = $feature->featureConfigHintLabelKey())
                                                                        <p
                                                                            class="text-[11px] text-gray-500 dark:text-gray-400">
                                                                            {{ __($hintKey) }}
                                                                        </p>
                                                                    @endif
                                                                    @foreach ($this->getFeatureConfigRows($feature->value) as $idx => $row)
                                                                        <div class="flex items-center gap-1.5"
                                                                            wire:key="feature-config-row-{{ $feature->inputIdPart() }}-{{ $idx }} {{ $addOnIdSuffix }}">
                                                                            <input type="text"
                                                                                wire:model.defer="featureConfigRows.{{ $feature->value }}.{{ $idx }}.key"
                                                                                placeholder="{{ __('module_event.config_key') }}"
                                                                                class="flex-1 min-w-0 p-1.5 text-xs bg-white border border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                                            <input type="text"
                                                                                wire:model.defer="featureConfigRows.{{ $feature->value }}.{{ $idx }}.value"
                                                                                placeholder="{{ __('module_event.config_value') }}"
                                                                                class="flex-1 min-w-0 p-1.5 text-xs bg-white border border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                                            <button type="button"
                                                                                wire:click="removeFeatureConfigRow('{{ $feature->value }}', {{ $idx }})"
                                                                                class="shrink-0 w-7 h-7 flex items-center justify-center text-red-600 hover:bg-red-50 rounded dark:hover:bg-gray-700"
                                                                                title="{{ __('module_base.delete') }}">
                                                                                −
                                                                            </button>
                                                                        </div>
                                                                    @endforeach
                                                                    <button type="button"
                                                                        wire:click="addFeatureConfigRow('{{ $feature->value }}')"
                                                                        class="w-7 h-7 flex items-center justify-center text-sm font-medium text-primary-600 dark:text-primary-400 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-100 dark:hover:bg-gray-700"
                                                                        title="{{ __('module_event.add_config_row') }}">
                                                                        +
                                                                    </button>
                                                                </div>
                                                            @else
                                                                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                                                    {{ __('module_event.feature_config_hint') }}
                                                                </div>
                                                            @endif
                                                        </details>
                                                    </div>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="border-t border-gray-200 dark:border-gray-700 pt-6 space-y-4">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ __('module_event.add_ons_modules') }}</h3>
                                @foreach ($moduleGroups as $categoryKey => $modules)
                                    <div class="space-y-3">
                                        <h4 class="text-xs font-medium text-gray-600 dark:text-gray-400">
                                            {{ __($AddOnEnum::categoryLabelKeyFor($categoryKey)) }}
                                        </h4>
                                        <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300 ml-3">
                                            @foreach ($modules as $module)
                                                @php
                                                    $mid = 'addon-' . $module->inputIdPart() . $addOnIdSuffix;
                                                    $mChecked = $this->hasAddOnModule($module->value);
                                                    $mDisabled = $module->isDefault();
                                                @endphp
                                                <li class="flex items-center gap-2">
                                                    <input type="checkbox" id="{{ $mid }}"
                                                        {{ $mChecked ? 'checked' : '' }}
                                                        {{ $mDisabled ? 'disabled' : '' }}
                                                        @if (!$mDisabled) wire:click="toggleAddOnModule('{{ $module->value }}')" @endif
                                                        class="checkbox-primary h-4 w-4 rounded border-gray-300 bg-gray-100 focus:ring-2 focus:ring-offset-0 dark:border-gray-600 dark:bg-gray-700 {{ $mDisabled ? 'opacity-70 cursor-not-allowed' : '' }}">
                                                    <label for="{{ $mid }}"
                                                        class="{{ $mDisabled ? 'text-gray-500 dark:text-gray-400' : '' }}">{{ __($module->labelKey()) }}</label>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            @if ($step > 1)
                                <button type="button" wire:click="previousStep"
                                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                                    {{ __('module_base.previous') }}
                                </button>
                            @endif
                            @if ($step < 4)
                                <button type="button" wire:click="nextStep"
                                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white rounded-lg btn-primary">
                                    {{ __('module_base.next') }}
                                </button>
                            @endif
                        </div>
                        <div class="flex items-center gap-3">
                            @can($TenantPermissionEnum::EVENT_VIEW->value)
                                <a href="{{ tenant_routes('admin.tenants.events.index') }}"
                                    class="inline-flex items-center px-2 py-1 text-xs font-medium text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700">{{ __('module_base.cancel') }}</a>
                            @endcan
                            <button type="submit"
                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-center text-white rounded-lg btn-primary">{{ __('module_base.save_changes') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
    @include('components.footer')
</div>
