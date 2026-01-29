<div>
    @if (session()->has("message"))
        <div class="mb-4 p-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
            {{ session("message") }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-6">
        <div class="space-y-4">
            @forelse ($settings as $index => $setting)
                <div class="p-4 bg-white border border-gray-200 rounded-lg dark:bg-gray-700 dark:border-gray-600">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                        <div class="md:col-span-5">
                            <label for="settings.{{ $index }}.key" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                {{ __("module_setting.key") }}
                            </label>
                            <input
                                type="text"
                                id="settings.{{ $index }}.key"
                                wire:model.defer="settings.{{ $index }}.key"
                                class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="{{ __("module_setting.key_placeholder") }}"
                            />
                            @error("settings.{{ $index }}.key")
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-6">
                            <label for="settings.{{ $index }}.value" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                {{ __("module_setting.value") }}
                            </label>
                            <input
                                type="text"
                                id="settings.{{ $index }}.value"
                                wire:model.defer="settings.{{ $index }}.value"
                                class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="{{ __("module_setting.value_placeholder") }}"
                            />
                            @error("settings.{{ $index }}.value")
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-1 my-1 flex items-center justify-center">
                            <button
                                type="button"
                                wire:click="removeSetting({{ $index }})"
                                class="w-8 h-8 flex items-center justify-center text-sm font-medium text-white bg-red-600 rounded-full hover:bg-red-700 focus:ring-4 focus:ring-red-300 dark:bg-red-500 dark:hover:bg-red-600 dark:focus:ring-red-700"
                                title="{{ __("module_base.delete") }}"
                            >
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-4 text-center text-sm text-gray-500 dark:text-gray-400 bg-white border border-gray-200 rounded-lg dark:bg-gray-700 dark:border-gray-600">
                    {{ __("module_setting.no_settings") }}
                </div>
            @endforelse
        </div>

        <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-700">
            <button
                type="button"
                wire:click="addSetting"
                class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700"
            >
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
                </svg>
                {{ __("module_setting.add_setting") }}
            </button>
            <button
                type="submit"
                class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-700"
            >
                {{ __("module_base.save") }}
            </button>
        </div>
    </form>
</div>
