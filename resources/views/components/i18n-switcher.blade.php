@php
    $i18n = app(\Src\V1\Api\I18N\Services\I18NService::class);
    $currentLang = $i18n->getLanguageFromSession(request());
    $availableLangs = $i18n->availableLangs();
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
        class="appearance-none rounded-md border border-gray-300 bg-white px-3 py-2 pr-7 text-sm text-gray-900 shadow-sm hover:bg-gray-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700 cursor-pointer"
    >
        @foreach ($availableLangs as $lang)
            <option value="{{ $lang }}" @selected($lang === $currentLang)>
                {{ strtoupper($lang) }}
            </option>
        @endforeach
    </select>
</div>
