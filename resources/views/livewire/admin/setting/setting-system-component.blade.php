<div>
    @if (session()->has('message'))
        <div class="mb-4 p-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400"
            role="alert">
            {{ session('message') }}
        </div>
    @endif

    <div class="space-y-6">
        <div class="space-y-4">
            @foreach ($settingModalRows as $index => $row)
                <div class="p-4 bg-white border border-gray-200 rounded-lg dark:bg-gray-700 dark:border-gray-600">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start">
                        <div class="md:col-span-4">
                            <label for="settingModalRows.{{ $index }}.key"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                {{ __('module_setting.key') }}
                            </label>
                            <input type="text" id="settingModalRows.{{ $index }}.key"
                                wire:model.defer="settingModalRows.{{ $index }}.key"
                                class="input-primary shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                placeholder="{{ __('module_setting.key_placeholder') }}" />
                            @error("settingModalRows.{$index}.key")
                                <p class="mt-1 text-sm text-[hsl(0,84.2%,60.2%)] dark:text-[hsl(0,62.8%,30.6%)]">
                                    {{ $message }}</p>
                            @enderror
                        </div>
                        <div class="min-w-0 md:col-span-7 space-y-3">
                            <div class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                {{ __('module_setting.value') }}
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <label for="settingModalRows.{{ $index }}.value_kind" class="sr-only">
                                        {{ __('module_setting.value') }}
                                    </label>
                                    <select id="settingModalRows.{{ $index }}.value_kind"
                                        wire:model.live="settingModalRows.{{ $index }}.value_kind"
                                        title="{{ __('module_content.value_kind_text') }} / {{ __('module_content.value_kind_file') }}"
                                        class="input-primary shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                        <option value="text">{{ __('module_content.value_kind_text') }}</option>
                                        <option value="file">{{ __('module_content.value_kind_file') }}</option>
                                    </select>
                                </div>
                                @php
                                    $valueKind = ($row['value_kind'] ?? 'text') === 'file' ? 'file' : 'text';
                                    $storedPath = (string) ($row['value'] ?? '');
                                    $isPublicPath =
                                        $storedPath !== '' &&
                                        str_starts_with($storedPath, 'setting-files/');
                                @endphp
                                @if ($valueKind === 'text')
                                    <input type="text" id="settingModalRows.{{ $index }}.value"
                                        wire:model.defer="settingModalRows.{{ $index }}.value"
                                        class="input-primary shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                        placeholder="{{ __('module_setting.value_placeholder') }}"
                                        aria-label="{{ __('module_setting.value') }}" />
                                @else
                                    <div
                                        class="min-w-0 overflow-visible rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-600 dark:bg-gray-800/50">
                                        @if ($isPublicPath)
                                            <a href="{{ asset('storage/' . $storedPath) }}" target="_blank"
                                                rel="noopener noreferrer"
                                                class="mb-4 inline-flex items-center gap-1.5 text-xs font-medium text-primary-600 hover:text-primary-700 hover:underline dark:text-primary-400 dark:hover:text-primary-300">
                                                <svg class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                </svg>
                                                {{ __('module_content.open_stored_file') }}
                                            </a>
                                        @endif
                                        <div class="min-w-0 space-y-3">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ __('module_content.value_file_upload') }}
                                            </div>
                                            <div
                                                class="flex min-w-0 flex-wrap items-center gap-3 rounded-lg border border-gray-200 bg-white p-3 shadow-sm dark:border-gray-600 dark:bg-gray-700">
                                                <label
                                                    class="inline-flex shrink-0 cursor-pointer items-center rounded-md border border-gray-300 bg-gray-50 px-4 py-2 text-xs font-medium text-gray-800 shadow-sm transition hover:bg-gray-100 focus-within:ring-2 focus-within:ring-primary-500 focus-within:ring-offset-1 dark:border-gray-500 dark:bg-gray-600 dark:text-gray-100 dark:hover:bg-gray-500 dark:focus-within:ring-offset-gray-800">
                                                    <input type="file"
                                                        wire:model="settingValueFiles.{{ $index }}"
                                                        class="sr-only" />
                                                    <span>{{ __('module_content.choose_file') }}</span>
                                                </label>
                                                <span
                                                    class="min-w-0 flex-1 truncate text-xs text-gray-600 dark:text-gray-300"
                                                    wire:loading.class="opacity-50"
                                                    wire:target="settingValueFiles.{{ $index }}">
                                                    <span wire:loading.remove
                                                        wire:target="settingValueFiles.{{ $index }}">
                                                        @php
                                                            $picked = $settingValueFiles[$index] ?? null;
                                                        @endphp
                                                        @if ($picked instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)
                                                            {{ $picked->getClientOriginalName() }}
                                                        @elseif ($isPublicPath)
                                                            {{ basename($storedPath) }}
                                                        @else
                                                            {{ __('module_content.value_file_none') }}
                                                        @endif
                                                    </span>
                                                    <span wire:loading wire:target="settingValueFiles.{{ $index }}"
                                                        class="italic">{{ __('module_content.value_file_uploading') }}</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @error("settingValueFiles.{$index}")
                                <p class="text-sm text-[hsl(0,84.2%,60.2%)] dark:text-[hsl(0,62.8%,30.6%)]">
                                    {{ $message }}</p>
                            @enderror
                            @error("settingModalRows.{$index}.value")
                                <p class="text-sm text-[hsl(0,84.2%,60.2%)] dark:text-[hsl(0,62.8%,30.6%)]">
                                    {{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-1 flex justify-center md:justify-end md:self-center">
                            <button type="button" wire:click="removeSettingRow({{ $index }})"
                                class="w-8 h-8 flex items-center justify-center text-sm font-medium text-white rounded-full btn-tertiary"
                                title="{{ __('module_base.delete') }}">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div
            class="flex justify-between items-center px-4 md:px-5 pt-4 pb-4 md:pb-5 border-t border-gray-200 dark:border-gray-700">
            <div class="flex flex-wrap items-center gap-3">
                <button type="button" wire:click="addSettingRow"
                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                    <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                            clip-rule="evenodd"></path>
                    </svg>
                    {{ __('module_content.add_row') }}
                </button>
                <button type="button" wire:click="saveSystemSettings"
                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-center text-white rounded-lg btn-primary">
                    {{ __('common.save') }}
                </button>
            </div>
        </div>
    </div>
</div>
