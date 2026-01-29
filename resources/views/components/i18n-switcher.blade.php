@php
    $i18n = app(\Src\V1\Api\I18N\Services\I18NService::class);
    $currentLang = $i18n->getLanguageFromSession(request());
    $availableLangs = $i18n->availableLangs();

    $flagMap = [

        'en' => '🇺🇸',
        'id' => '🇮🇩',
    ];
@endphp

@push('styles')
<link href="{{ asset('css/components/i18n.css') }}" rel="stylesheet" />
@endpush

@push('scripts-end')
<script src="{{ asset('js/components/i18n.js') }}"></script>
@endpush

<div class="relative inline-flex">
    <select
        data-lang-switcher
        data-i18n-base="{{ url('i18n') }}"
        class="appearance-none rounded-lg bg-white px-3 py-2 pr-7 text-sm text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-gray-600 dark:focus:ring-gray-700 cursor-pointer"
    >
        @foreach ($availableLangs as $lang)
            <option value="{{ $lang }}" @selected($lang === $currentLang)>
                {{ $flagMap[$lang] ?? '🏳️' }} {{ strtoupper($lang) }}
            </option>
        @endforeach
    </select>
</div>
