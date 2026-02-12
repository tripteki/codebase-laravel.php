<div class="flex items-center space-x-1.5">
    <a
        href="{{ route('admin.activities.show', $activity) }}"
        class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-900 focus:ring-4 focus:ring-gray-100 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700"
    >
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7Z" />
        </svg>
        {{ __("module_base.view") }}
    </a>
</div>
