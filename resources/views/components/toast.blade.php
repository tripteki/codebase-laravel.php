@php
    $toastId = 'toast-' . uniqid();
@endphp

@push('styles')
<link href="{{ asset('css/components/toast.css') }}" rel="stylesheet" />
@endpush

@push('scripts-end')
<script src="{{ asset('js/components/toast.js') }}"></script>
@endpush

@if (session()->has('toast'))
    @php
        $toast = session('toast');
        $type = $toast['type'] ?? 'success';
        $message = $toast['message'] ?? '';
    @endphp

    <div
        id="{{ $toastId }}"
        class="fixed flex items-center w-full max-w-xs p-4 text-gray-500 bg-white rounded-lg shadow border border-gray-200 dark:text-gray-400 dark:bg-gray-800 dark:border-gray-700 top-5 end-5 z-50 transform translate-x-full transition-all duration-300 ease-in-out"
        role="alert"
    >
        @if ($type === 'success')
            <div class="inline-flex items-center justify-center shrink-0 w-7 h-7 text-green-500 bg-green-100 rounded dark:bg-green-800 dark:text-green-200">
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11.917 9.724 16.5 19 7.5"/>
                </svg>
                <span class="sr-only">Check icon</span>
            </div>
        @elseif ($type === 'error')
            <div class="inline-flex items-center justify-center shrink-0 w-7 h-7 text-red-500 bg-red-100 rounded dark:bg-red-800 dark:text-red-200">
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/>
                </svg>
                <span class="sr-only">Error icon</span>
            </div>
        @elseif ($type === 'info')
            <div class="inline-flex items-center justify-center shrink-0 w-7 h-7 text-blue-500 bg-blue-100 rounded dark:bg-blue-800 dark:text-blue-200">
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
                <span class="sr-only">Info icon</span>
            </div>
        @endif

        <div class="ms-3 text-sm font-normal">{{ $message }}</div>
        <button
            type="button"
            class="ms-auto flex items-center justify-center text-gray-400 hover:text-gray-900 bg-transparent hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 rounded-lg text-sm h-8 w-8 dark:hover:bg-gray-600 dark:text-gray-500 dark:hover:text-white dark:focus:ring-gray-700"
            data-dismiss-target="#{{ $toastId }}"
            aria-label="Close"
        >
            <span class="sr-only">Close</span>
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/>
            </svg>
        </button>
    </div>
@endif
