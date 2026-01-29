<div>
    @if (session()->has("message"))
        <div class="mb-4 p-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
            {{ session("message") }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-8">
        <div class="p-4 bg-white border border-gray-200 rounded-lg dark:bg-gray-700 dark:border-gray-600">
            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                {{ __("module_setting.account") }}
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        {{ __("module_setting.name") }}
                    </label>
                    <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">
                        {{ __("module_setting.name_hint") }}
                    </p>
                    <input
                        type="text"
                        id="name"
                        wire:model.defer="name"
                        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="{{ __("module_setting.name_placeholder") }}"
                    />
                    @error("name")
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        {{ __("module_setting.email") }}
                    </label>
                    <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">
                        {{ __("module_setting.email_hint") }}
                    </p>
                    <input
                        type="email"
                        id="email"
                        wire:model.defer="email"
                        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="{{ __("module_setting.email_placeholder") }}"
                    />
                    @error("email")
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="p-4 bg-white border border-gray-200 rounded-lg dark:bg-gray-700 dark:border-gray-600">
            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                {{ __("module_setting.basic") }}
            </h3>
            <div class="space-y-6 flex flex-col items-center">
                <div class="w-full max-w-md">
                    <label for="avatar" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white text-center">
                        {{ __("module_setting.avatar") }}
                    </label>
                    <p class="mb-2 text-xs text-gray-500 dark:text-gray-400 text-center">
                        {{ __("module_setting.avatar_hint") }}
                    </p>
                    <div class="flex flex-col items-center gap-4">
                        <div class="flex-shrink-0">
                            <div class="relative w-24 h-24">
                                @if ($avatar)
                                    <img
                                        src="{{ $avatar->temporaryUrl() }}"
                                        alt="Avatar Preview"
                                        class="w-24 h-24 rounded-full object-cover border-4 border-blue-300 dark:border-blue-600 shadow-sm"
                                    />
                                    <div wire:loading wire:target="avatar" class="absolute inset-0 rounded-full bg-black bg-opacity-30 flex items-center justify-center">
                                        <div class="w-8 h-8 border-4 border-white border-t-transparent rounded-full animate-spin"></div>
                                    </div>
                                @elseif ($avatarUrl)
                                    <img
                                        src="{{ asset('storage/' . $avatarUrl) }}"
                                        alt="Avatar"
                                        class="w-24 h-24 rounded-full object-cover border-4 border-gray-200 dark:border-gray-700 shadow-sm"
                                    />
                                @else
                                    <div class="w-24 h-24 rounded-full bg-gray-200 dark:bg-gray-300 border-4 border-gray-200 dark:border-gray-300 flex items-center justify-center shadow-sm">
                                        <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="w-full">
                            <input
                                type="file"
                                id="avatar"
                                wire:model.live="avatar"
                                accept="image/*"
                                class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                            />
                            @error("avatar")
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="w-full max-w-md">
                    <label for="fullName" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white text-center">
                        {{ __("module_setting.full_name") }}
                    </label>
                    <p class="mb-2 text-xs text-gray-500 dark:text-gray-400 text-center">
                        {{ __("module_setting.full_name_placeholder") }}
                    </p>
                    <input
                        type="text"
                        id="fullName"
                        wire:model.defer="fullName"
                        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="{{ __("module_setting.full_name_placeholder") }}"
                    />
                    @error("fullName")
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="p-4 bg-white border border-gray-200 rounded-lg dark:bg-gray-700 dark:border-gray-600">
            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                {{ __("module_setting.security") }}
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        {{ __("module_setting.password") }}
                    </label>
                    <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">
                        {{ __("module_setting.password_hint") }}
                    </p>
                    <div class="relative">
                        <input
                            type="password"
                            id="password"
                            wire:model.defer="password"
                            class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 pr-10 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="{{ __("module_setting.password_placeholder") }}"
                        >
                        <button
                            type="button"
                            data-password-toggle="password"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
                        >
                            <svg id="password-eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7Z" />
                            </svg>
                            <svg id="password-eye-off-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0 1 12 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 0 1 1.563-3.029m5.858.908a3 3 0 1 1 4.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0 1 12 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 0 1-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                    @error("password")
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="passwordConfirmation" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        {{ __("module_setting.password_confirmation") }}
                    </label>
                    <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">
                        {{ __("module_setting.password_confirmation_hint") }}
                    </p>
                    <div class="relative">
                        <input
                            type="password"
                            id="passwordConfirmation"
                            wire:model.defer="passwordConfirmation"
                            class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 pr-10 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="{{ __("module_setting.password_confirmation_placeholder") }}"
                        >
                        <button
                            type="button"
                            data-password-toggle="passwordConfirmation"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
                        >
                            <svg id="passwordConfirmation-eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7Z" />
                            </svg>
                            <svg id="passwordConfirmation-eye-off-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0 1 12 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 0 1 1.563-3.029m5.858.908a3 3 0 1 1 4.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0 1 12 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 0 1-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                    @error("passwordConfirmation")
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
            <button
                type="submit"
                class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-700"
            >
                {{ __("module_base.save") }}
            </button>
        </div>
    </form>
</div>

@push('styles')
<link href="{{ asset('css/module/user.css') }}" rel="stylesheet" />
@endpush

@push('scripts-end')
<script src="{{ asset('js/module/user.js') }}"></script>
@endpush
