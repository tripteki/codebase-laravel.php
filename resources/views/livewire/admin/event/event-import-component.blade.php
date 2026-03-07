<div>
    <button type="button" wire:click="openModal"
        class="inline-flex items-center justify-center px-2 py-1 text-xs font-medium text-white rounded-lg btn-secondary">
        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
        </svg>
        {{ __('module_base.import') }}
    </button>

    @if ($showModal)
        <div x-data="{ show: @entangle('showModal') }" x-show="show" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 backdrop-blur-sm"
            @click.away="show = false">
            <div class="relative w-full max-w-md bg-white rounded-lg shadow dark:bg-gray-800" @click.stop>
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ __('module_event.import_events') }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ __('module_base.import_notification_info') }}
                        </p>
                    </div>
                    <button type="button" wire:click="closeModal"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">{{ __('common.close') }}</span>
                    </button>
                </div>
                <div class="p-4 md:p-5">
                    <form wire:submit.prevent="import">
                        <div class="mb-4">
                            <label for="event-import-file"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                {{ __('module_base.select_file') }}
                            </label>
                            <input type="file" id="event-import-file" wire:model="importFile"
                                accept=".csv,.xls,.xlsx"
                                class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                            @error('importFile')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                {{ __('module_base.import_file_hint') }}
                            </p>
                        </div>
                        <div class="flex items-center justify-end gap-3">
                            <button type="button" wire:click="closeModal"
                                class="py-1.5 px-3 text-xs font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                                {{ __('module_base.cancel') }}
                            </button>
                            <button type="submit"
                                class="py-1.5 px-3 text-xs font-medium text-center text-white rounded-lg btn-secondary">
                                {{ __('module_base.import') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
