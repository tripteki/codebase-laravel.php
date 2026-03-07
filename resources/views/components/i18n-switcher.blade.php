@php
    use Src\V1\Api\I18N\Enums\LanguageEnum;

    $i18n = app(\Src\V1\Api\I18N\Services\I18NService::class);
    $currentLang = $i18n->getLanguageFromSession(request());
    $availableLangs = $i18n->availableLangs();
    $langLabels = LanguageEnum::labels();

    $position = $position ?? 'bottom';
    $positionClass = $position === 'top' ? '-top-24' : '-bottom-24';
    $onDark = (bool) ($onDark ?? false);
    $onLight = ! $onDark;
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
        @class([
            'i18n-trigger inline-flex items-center gap-2 rounded-full px-3 py-2 text-sm font-medium transition-all focus:outline-none focus:ring-2',
            'text-gray-600 hover:bg-gray-100 hover:text-gray-900 focus:ring-gray-200 dark:text-gray-300 dark:hover:bg-gray-700/80 dark:hover:text-white dark:focus:ring-gray-600' => ! $onDark,
            'text-white/80 hover:bg-white/10 hover:text-white focus:ring-white/25' => $onDark,
        ])
    >
        @if ($currentLang === LanguageEnum::ENGLISH->value)
            <svg class="h-4 w-4 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" width="1900" height="1000" viewBox="0 0 1900 1000">
                <rect width="1900" height="1000" fill="#B22234"/>

                <rect y="76.923" width="1900" height="76.923" fill="#fff"/>
                <rect y="230.769" width="1900" height="76.923" fill="#fff"/>
                <rect y="384.615" width="1900" height="76.923" fill="#fff"/>
                <rect y="538.461" width="1900" height="76.923" fill="#fff"/>
                <rect y="692.307" width="1900" height="76.923" fill="#fff"/>
                <rect y="846.153" width="1900" height="76.923" fill="#fff"/>

                <rect width="760" height="538.462" fill="#3C3B6E"/>

                <defs>
                    <polygon id="star" points="0,-26 7.64,-8.45 24.73,-8.03 12.36,3.23 16.18,20.97 0,11 -16.18,20.97 -12.36,3.23 -24.73,-8.03 -7.64,-8.45" fill="#fff"/>
                </defs>

                <g transform="translate(63.33,59.83)">
                    <g>
                        <use href="#star" x="0" y="0"/>
                        <use href="#star" x="108.57" y="0"/>
                        <use href="#star" x="217.14" y="0"/>
                        <use href="#star" x="325.71" y="0"/>
                        <use href="#star" x="434.28" y="0"/>
                        <use href="#star" x="542.85" y="0"/>
                    </g>
                    <g transform="translate(54.285,59.83)">
                        <use href="#star" x="0" y="0"/>
                        <use href="#star" x="108.57" y="0"/>
                        <use href="#star" x="217.14" y="0"/>
                        <use href="#star" x="325.71" y="0"/>
                        <use href="#star" x="434.28" y="0"/>
                    </g>
                    <g transform="translate(0,119.66)">
                        <use href="#star" x="0" y="0"/>
                        <use href="#star" x="108.57" y="0"/>
                        <use href="#star" x="217.14" y="0"/>
                        <use href="#star" x="325.71" y="0"/>
                        <use href="#star" x="434.28" y="0"/>
                        <use href="#star" x="542.85" y="0"/>
                    </g>
                    <g transform="translate(54.285,179.49)">
                        <use href="#star" x="0" y="0"/>
                        <use href="#star" x="108.57" y="0"/>
                        <use href="#star" x="217.14" y="0"/>
                        <use href="#star" x="325.71" y="0"/>
                        <use href="#star" x="434.28" y="0"/>
                    </g>
                    <g transform="translate(0,239.32)">
                        <use href="#star" x="0" y="0"/>
                        <use href="#star" x="108.57" y="0"/>
                        <use href="#star" x="217.14" y="0"/>
                        <use href="#star" x="325.71" y="0"/>
                        <use href="#star" x="434.28" y="0"/>
                        <use href="#star" x="542.85" y="0"/>
                    </g>
                    <g transform="translate(54.285,299.15)">
                        <use href="#star" x="0" y="0"/>
                        <use href="#star" x="108.57" y="0"/>
                        <use href="#star" x="217.14" y="0"/>
                        <use href="#star" x="325.71" y="0"/>
                        <use href="#star" x="434.28" y="0"/>
                    </g>
                    <g transform="translate(0,358.98)">
                        <use href="#star" x="0" y="0"/>
                        <use href="#star" x="108.57" y="0"/>
                        <use href="#star" x="217.14" y="0"/>
                        <use href="#star" x="325.71" y="0"/>
                        <use href="#star" x="434.28" y="0"/>
                        <use href="#star" x="542.85" y="0"/>
                    </g>
                    <g transform="translate(54.285,418.81)">
                        <use href="#star" x="0" y="0"/>
                        <use href="#star" x="108.57" y="0"/>
                        <use href="#star" x="217.14" y="0"/>
                        <use href="#star" x="325.71" y="0"/>
                        <use href="#star" x="434.28" y="0"/>
                    </g>
                    <g transform="translate(0,478.64)">
                        <use href="#star" x="0" y="0"/>
                        <use href="#star" x="108.57" y="0"/>
                        <use href="#star" x="217.14" y="0"/>
                        <use href="#star" x="325.71" y="0"/>
                        <use href="#star" x="434.28" y="0"/>
                        <use href="#star" x="542.85" y="0"/>
                    </g>
                </g>
            </svg>
        @elseif ($currentLang === LanguageEnum::INDONESIA->value)
            <svg class="h-4 w-4 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" width="900" height="600" viewBox="0 0 900 600">
                <rect width="900" height="300" y="0" fill="#CE1126"/>
                <rect width="900" height="300" y="300" fill="#FFFFFF"/>
            </svg>
        @elseif ($currentLang === LanguageEnum::MALAYSIA->value)
            <svg class="h-4 w-4 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" width="1400" height="700" viewBox="0 0 1400 700">
                <rect width="1400" height="700" fill="#fff"/>
                <rect y="0" width="1400" height="50" fill="#CC0001"/>
                <rect y="100" width="1400" height="50" fill="#CC0001"/>
                <rect y="200" width="1400" height="50" fill="#CC0001"/>
                <rect y="300" width="1400" height="50" fill="#CC0001"/>
                <rect y="400" width="1400" height="50" fill="#CC0001"/>
                <rect y="500" width="1400" height="50" fill="#CC0001"/>
                <rect y="600" width="1400" height="50" fill="#CC0001"/>

                <rect width="700" height="400" fill="#010066"/>

                <circle cx="280" cy="200" r="115" fill="#FFCC00"/>
                <circle cx="320" cy="200" r="95" fill="#010066"/>

                <defs>
                    <polygon id="point14" points="
                      0,-85
                      14.73,-30.56
                      66.45,-52.99
                      38.23,-4.74
                      84.02,18.91
                      31.57,25.06
                      37.82,78.68
                      0,40
                      -37.82,78.68
                      -31.57,25.06
                      -84.02,18.91
                      -38.23,-4.74
                      -66.45,-52.99
                      -14.73,-30.56
                    " fill="#FFCC00"/>
                </defs>
                <use href="#point14" x="470" y="200"/>
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
        class="absolute {{ $positionClass }} right-0 z-50 {{ $position === 'top' ? 'mb-2' : 'mt-2' }} w-38 origin-top-right rounded-lg bg-white shadow-lg ring-1 ring-black/5 focus:outline-none dark:bg-gray-800 dark:ring-white/10"
    >
        <div class="py-2" role="menu">
            @foreach ($availableLangs as $lang)
                <a
                    href="{{ url('i18n/' . $lang) }}"
                    data-lang-switcher-item
                    data-lang="{{ $lang }}"
                    data-i18n-base="{{ url('i18n') }}"
                    class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700/50 {{ $lang === $currentLang ? 'bg-gray-50 dark:bg-gray-700/50' : '' }} transition-colors"
                    role="menuitem"
                >
                    @if ($lang === LanguageEnum::ENGLISH->value)
                        <svg class="h-4 w-4 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" width="1900" height="1000" viewBox="0 0 1900 1000">
                            <rect width="1900" height="1000" fill="#B22234"/>

                            <rect y="76.923" width="1900" height="76.923" fill="#fff"/>
                            <rect y="230.769" width="1900" height="76.923" fill="#fff"/>
                            <rect y="384.615" width="1900" height="76.923" fill="#fff"/>
                            <rect y="538.461" width="1900" height="76.923" fill="#fff"/>
                            <rect y="692.307" width="1900" height="76.923" fill="#fff"/>
                            <rect y="846.153" width="1900" height="76.923" fill="#fff"/>

                            <rect width="760" height="538.462" fill="#3C3B6E"/>

                            <defs>
                                <polygon id="star" points="0,-26 7.64,-8.45 24.73,-8.03 12.36,3.23 16.18,20.97 0,11 -16.18,20.97 -12.36,3.23 -24.73,-8.03 -7.64,-8.45" fill="#fff"/>
                            </defs>

                            <g transform="translate(63.33,59.83)">
                                <g>
                                    <use href="#star" x="0" y="0"/>
                                    <use href="#star" x="108.57" y="0"/>
                                    <use href="#star" x="217.14" y="0"/>
                                    <use href="#star" x="325.71" y="0"/>
                                    <use href="#star" x="434.28" y="0"/>
                                    <use href="#star" x="542.85" y="0"/>
                                </g>
                                <g transform="translate(54.285,59.83)">
                                    <use href="#star" x="0" y="0"/>
                                    <use href="#star" x="108.57" y="0"/>
                                    <use href="#star" x="217.14" y="0"/>
                                    <use href="#star" x="325.71" y="0"/>
                                    <use href="#star" x="434.28" y="0"/>
                                </g>
                                <g transform="translate(0,119.66)">
                                    <use href="#star" x="0" y="0"/>
                                    <use href="#star" x="108.57" y="0"/>
                                    <use href="#star" x="217.14" y="0"/>
                                    <use href="#star" x="325.71" y="0"/>
                                    <use href="#star" x="434.28" y="0"/>
                                    <use href="#star" x="542.85" y="0"/>
                                </g>
                                <g transform="translate(54.285,179.49)">
                                    <use href="#star" x="0" y="0"/>
                                    <use href="#star" x="108.57" y="0"/>
                                    <use href="#star" x="217.14" y="0"/>
                                    <use href="#star" x="325.71" y="0"/>
                                    <use href="#star" x="434.28" y="0"/>
                                </g>
                                <g transform="translate(0,239.32)">
                                    <use href="#star" x="0" y="0"/>
                                    <use href="#star" x="108.57" y="0"/>
                                    <use href="#star" x="217.14" y="0"/>
                                    <use href="#star" x="325.71" y="0"/>
                                    <use href="#star" x="434.28" y="0"/>
                                    <use href="#star" x="542.85" y="0"/>
                                </g>
                                <g transform="translate(54.285,299.15)">
                                    <use href="#star" x="0" y="0"/>
                                    <use href="#star" x="108.57" y="0"/>
                                    <use href="#star" x="217.14" y="0"/>
                                    <use href="#star" x="325.71" y="0"/>
                                    <use href="#star" x="434.28" y="0"/>
                                </g>
                                <g transform="translate(0,358.98)">
                                    <use href="#star" x="0" y="0"/>
                                    <use href="#star" x="108.57" y="0"/>
                                    <use href="#star" x="217.14" y="0"/>
                                    <use href="#star" x="325.71" y="0"/>
                                    <use href="#star" x="434.28" y="0"/>
                                    <use href="#star" x="542.85" y="0"/>
                                </g>
                                <g transform="translate(54.285,418.81)">
                                    <use href="#star" x="0" y="0"/>
                                    <use href="#star" x="108.57" y="0"/>
                                    <use href="#star" x="217.14" y="0"/>
                                    <use href="#star" x="325.71" y="0"/>
                                    <use href="#star" x="434.28" y="0"/>
                                </g>
                                <g transform="translate(0,478.64)">
                                    <use href="#star" x="0" y="0"/>
                                    <use href="#star" x="108.57" y="0"/>
                                    <use href="#star" x="217.14" y="0"/>
                                    <use href="#star" x="325.71" y="0"/>
                                    <use href="#star" x="434.28" y="0"/>
                                    <use href="#star" x="542.85" y="0"/>
                                </g>
                            </g>
                        </svg>
                    @elseif ($lang === LanguageEnum::INDONESIA->value)
                        <svg class="h-4 w-4 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" width="900" height="600" viewBox="0 0 900 600">
                            <rect width="900" height="300" y="0" fill="#CE1126"/>
                            <rect width="900" height="300" y="300" fill="#FFFFFF"/>
                        </svg>
                    @elseif ($lang === LanguageEnum::MALAYSIA->value)
                        <svg class="h-4 w-4 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" width="1400" height="700" viewBox="0 0 1400 700">
                            <rect width="1400" height="700" fill="#fff"/>
                            <rect y="0" width="1400" height="50" fill="#CC0001"/>
                            <rect y="100" width="1400" height="50" fill="#CC0001"/>
                            <rect y="200" width="1400" height="50" fill="#CC0001"/>
                            <rect y="300" width="1400" height="50" fill="#CC0001"/>
                            <rect y="400" width="1400" height="50" fill="#CC0001"/>
                            <rect y="500" width="1400" height="50" fill="#CC0001"/>
                            <rect y="600" width="1400" height="50" fill="#CC0001"/>

                            <rect width="700" height="400" fill="#010066"/>

                            <circle cx="280" cy="200" r="115" fill="#FFCC00"/>
                            <circle cx="320" cy="200" r="95" fill="#010066"/>

                            <defs>
                                <polygon id="point14" points="
                                  0,-85
                                  14.73,-30.56
                                  66.45,-52.99
                                  38.23,-4.74
                                  84.02,18.91
                                  31.57,25.06
                                  37.82,78.68
                                  0,40
                                  -37.82,78.68
                                  -31.57,25.06
                                  -84.02,18.91
                                  -38.23,-4.74
                                  -66.45,-52.99
                                  -14.73,-30.56
                                " fill="#FFCC00"/>
                            </defs>
                            <use href="#point14" x="470" y="200"/>
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
