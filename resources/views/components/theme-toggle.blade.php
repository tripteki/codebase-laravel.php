@push('styles')
<link href="{{ asset('css/components/theme-toggle.css') }}" rel="stylesheet" />
@endpush

@push('scripts-end')
<script src="{{ asset('js/components/theme-toggle.js') }}"></script>
@endpush

<button
    type="button"
    data-theme-toggle
    class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-gray-300 bg-white text-sm text-gray-900 shadow-sm hover:bg-gray-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700 cursor-pointer"
    aria-label="{{ __('common.toggle_theme') }}"
>
    <span class="sr-only">{{ __('common.toggle_theme') }}</span>
    <span aria-hidden="true" data-theme-icon>☀️</span>
</button>
