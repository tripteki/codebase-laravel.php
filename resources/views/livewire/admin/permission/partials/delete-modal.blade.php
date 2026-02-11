<div
    x-data="{ show: false, permissionId: null, permissionName: '' }"
    x-show="show"
    x-cloak
    id="delete-permission-modal"
    tabindex="-1"
    class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 backdrop-blur-sm"
    @click.away="show = false"
    @open-delete-modal.window="show = true; permissionId = '{{ $row->id ?? null }}'; permissionName = '{{ $row->name ?? null }}'"
    @close-delete-modal.window="show = false"
>
    <div
        class="relative w-full max-w-md bg-white rounded-lg shadow dark:bg-gray-800"
        @click.stop
    >
        <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                {{ __("module_base.confirm_delete") }}
            </h3>
            <button
                type="button"
                @click="show = false"
                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
            >
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
                <span class="sr-only">{{ __("common.close") }}</span>
            </button>
        </div>
        <div class="p-4 md:p-5">
            <p class="mb-4 text-center text-sm text-gray-500 dark:text-gray-400" x-text="'{{ __("module_base.confirm_delete_message") }}'.replace(':name', permissionName)"></p>
            <div class="flex items-center justify-end gap-3">
                <button
                    type="button"
                    @click="show = false"
                    class="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 flex items-center justify-center"
                >
                    {{ __("module_base.cancel") }}
                </button>
                <button
                    type="button"
                    wire:click="deletePermission({ permissionId }); show = false"
                    class="py-2.5 px-5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 dark:bg-red-400 dark:hover:bg-red-500 dark:focus:ring-red-700 flex items-center justify-center"
                >
                    {{ __("module_base.delete") }}
                </button>
            </div>
        </div>
    </div>
</div>
