<div class="flex items-center space-x-1.5">
    <a
        href="{{ route('admin.permissions.show', $permission) }}"
        class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-900 focus:ring-4 focus:ring-gray-100 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700"
    >
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7Z" />
        </svg>
        {{ __("module_base.view") }}
    </a>

    <a
        href="{{ route('admin.permissions.edit', $permission) }}"
        class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-yellow-600 rounded-lg hover:bg-yellow-700 focus:ring-4 focus:ring-yellow-300 dark:bg-yellow-400 dark:hover:bg-yellow-500 dark:focus:ring-yellow-700"
    >
        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M17.414 2.586a2 2 0 0 0-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 0 0 0-2.828Z" />
            <path fill-rule="evenodd" d="M2 6a2 2 0 0 1 2-2h4a1 1 0 1 1 0 2H4v10h10v-4a 1 1 0 1 1 2 0v4a 2 2 0 0 1-2 2H4a 2 2 0 0 1-2-2V6Z" clip-rule="evenodd" />
        </svg>
        {{ __("module_base.edit") }}
    </a>

    <button
        type="button"
        wire:click="confirmDelete('{{ $permission->id }}')"
        class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:ring-red-300 dark:bg-red-400 dark:hover:bg-red-500 dark:focus:ring-red-700"
    >
        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M9 2a1 1 0 0 0-.894.553L7.382 4H4a1 1 0 0 0 0 2v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V6a1 1 0 1 0 0-2h-3.382l-.724-1.447A1 1 0 0 0 11 2H9ZM7 8a1 1 0 0 1 2 0v6a1 1 0 1 1-2 0V8Zm5-1a1 1 0 0 0-1 1v6a 1 1 0 1 0 2 0V8a 1 1 0 0 0-1-1Z" clip-rule="evenodd" />
        </svg>
        {{ __("module_base.delete") }}
    </button>
</div>
