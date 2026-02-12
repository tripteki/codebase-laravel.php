@php
    use Src\V1\Api\I18N\Enums\LanguageEnum;

    $i18n = app(\Src\V1\Api\I18N\Services\I18NService::class);
    $currentLang = $i18n->getLanguageFromSession(request());
    $availableLangs = $i18n->availableLangs();
    $langLabels = LanguageEnum::labels();

    $position = $position ?? 'bottom';
    $positionClass = $position === 'top' ? '-top-24' : '-bottom-24';
@endphp

@push('styles')
<link href="{{ asset('css/components/i18n.css') }}" rel="stylesheet" />
@endpush

@push('scripts-end')
<script src="{{ asset('js/components/i18n.js') }}"></script>
@endpush

<div class="relative" x-data="{ open: false }">
    <button
        type="button"
        @click="open = !open"
        @click.away="open = false"
        class="inline-flex items-center gap-2 rounded-lg bg-white px-3 py-2 text-sm font-medium text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-gray-600 dark:focus:ring-gray-700 transition-colors"
    >
        @if ($currentLang === LanguageEnum::ENGLISH->value)
            <svg class="h-4 w-4 flex-shrink-0" viewBox="0 0 640 480" xmlns="http://www.w3.org/2000/svg">
                <path fill="#b22234" d="M0 0h640v480H0z"/>
                <path fill="#fff" d="M0 0h640v37.9H0zM0 75.8h640v37.9H0zM0 151.6h640v37.9H0zM0 227.4h640v37.9H0zM0 303.2h640v37.9H0zM0 379h640v37.9H0z"/>
                <path fill="#3c3b6e" d="M0 0h256v256H0z"/>
                <path fill="#fff" d="M42.7 0h42.7v256h-42.7zM128 0h42.7v256H128zM213.3 0h42.7v256h-42.7zM0 42.7h256v42.7H0zM0 128h256v42.7H0zM0 213.3h256v42.7H0z"/>
            </svg>
        @elseif ($currentLang === LanguageEnum::INDONESIA->value)
            <svg class="h-4 w-4 flex-shrink-0" viewBox="0 0 640 480" xmlns="http://www.w3.org/2000/svg">
                <path fill="#e70011" d="M0 0h640v240H0z"/>
                <path fill="#fff" d="M0 240h640v240H0z"/>
            </svg>
        @else
            <svg class="h-4 w-4 flex-shrink-0" viewBox="0 0 640 480" xmlns="http://www.w3.org/2000/svg">
                <path fill="#fff" d="M0 0h640v480H0z"/>
            </svg>
        @endif
        <span>{{ strtoupper($currentLang) }}</span>
        <svg class="h-3 w-3 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        x-cloak
        class="absolute {{ $positionClass }} right-0 z-50 {{ $position === 'top' ? 'mb-2' : 'mt-2' }} w-38 origin-top-right rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none dark:bg-gray-700"
    >
        <div class="py-1" role="menu">
            @foreach ($availableLangs as $lang)
                <a
                    href="{{ url('i18n/' . $lang) }}"
                    data-lang-switcher-item
                    data-lang="{{ $lang }}"
                    data-i18n-base="{{ url('i18n') }}"
                    class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 {{ $lang === $currentLang ? 'bg-gray-50 dark:bg-gray-800' : '' }}"
                    role="menuitem"
                >
                    @if ($lang === LanguageEnum::ENGLISH->value)
                        <svg class="h-4 w-4 flex-shrink-0" viewBox="0 0 640 480" xmlns="http://www.w3.org/2000/svg">
                            <path fill="#b22234" d="M0 0h640v480H0z"/>
                            <path fill="#fff" d="M0 0h640v37.9H0zM0 75.8h640v37.9H0zM0 151.6h640v37.9H0zM0 227.4h640v37.9H0zM0 303.2h640v37.9H0zM0 379h640v37.9H0z"/>
                            <path fill="#3c3b6e" d="M0 0h256v256H0z"/>
                            <path fill="#fff" d="M42.7 0h42.7v256h-42.7zM128 0h42.7v256H128zM213.3 0h42.7v256h-42.7zM0 42.7h256v42.7H0zM0 128h256v42.7H0zM0 213.3h256v42.7H0z"/>
                        </svg>
                    @elseif ($lang === LanguageEnum::INDONESIA->value)
                        <svg class="h-4 w-4 flex-shrink-0" viewBox="0 0 640 480" xmlns="http://www.w3.org/2000/svg">
                            <path fill="#e70011" d="M0 0h640v240H0z"/>
                            <path fill="#fff" d="M0 240h640v240H0z"/>
                        </svg>
                    @else
                        <svg class="h-4 w-4 flex-shrink-0" viewBox="0 0 640 480" xmlns="http://www.w3.org/2000/svg">
                            <path fill="#fff" d="M0 0h640v480H0z"/>
                        </svg>
                    @endif
                    <span class="flex-1">{{ $langLabels[$lang] ?? strtoupper($lang) }}</span>
                    @if ($lang === $currentLang)
                        <svg class="h-4 w-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</div>
