<div class="min-h-screen flex flex-col bg-gray-100 lg:pl-64 dark:bg-gray-900" data-sidebar-content>
    @include('components.header')

    <main id="main-content" class="flex-1 px-4 py-6 lg:px-6">
        <div class="mx-auto">
            <div class="mb-4">
                <nav class="flex mb-5" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 text-sm font-medium md:space-x-2">
                        <li class="inline-flex items-center">
                            <a href="{{ tenant_routes('admin.dashboard.index') }}"
                                class="inline-flex items-center text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white">
                                <svg class="w-4 h-4 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M10.707 2.293a1 1 0 0 0-1.414 0l-7 7a1 1 0 0 0 1.414 1.414L4 10.414V17a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-6.586l.293.293a1 1 0 0 0 1.414-1.414l-7-7z">
                                    </path>
                                </svg>
                                {{ __('common.home') }}
                            </a>
                        </li>
                        @can(\Src\V1\Api\User\Enums\PermissionEnum::USER_VIEW->value)
                            <li>
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-gray-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M7.293 14.707a1 1 0 0 1 0-1.414L10.586 10 7.293 6.707a1 1 0 0 1 1.414-1.414l4 4a1 1 0 0 1 0 1.414l-4 4a1 1 0 0 1-1.414 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <a href="{{ tenant_routes('admin.users.index') }}"
                                        class="inline-flex items-center ml-1 text-gray-700 hover:text-primary-600 md:ml-2 dark:text-gray-300 dark:hover:text-white">
                                        <svg class="w-4 h-4 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z">
                                            </path>
                                        </svg>
                                        {{ __('module_user.title') }}
                                    </a>
                                </div>
                            </li>
                        @endcan
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 0 1 0-1.414L10.586 10 7.293 6.707a1 1 0 0 1 1.414-1.414l4 4a1 1 0 0 1 0 1.414l-4 4a1 1 0 0 1-1.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="inline-flex items-center ml-1 text-gray-400 md:ml-2 dark:text-gray-500"
                                    aria-current="page">
                                    <svg class="w-4 h-4 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    {{ __('module_base.create') }}
                                </span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">
                    {{ __('module_user.add_new_user') }}</h1>
            </div>

            <div class="bg-white rounded-lg shadow dark:bg-gray-800">
                <div class="flex justify-center w-full">
                    <ol
                        class="flex flex-wrap justify-center items-center w-full max-w-4xl p-4 text-sm font-medium text-center text-gray-500 sm:text-base sm:p-6 gap-x-3 gap-y-3 sm:gap-x-0 sm:gap-y-0">
                        <li
                            class="flex flex-none sm:flex-1 items-center {{ $step >= 1 ? 'text-primary-600 dark:text-primary-500' : '' }} after:content-[''] after:w-full after:h-1 after:border-b after:border-gray-200 after:border-1 after:hidden sm:after:inline-block after:mx-2 xl:after:mx-4 dark:after:border-gray-700">
                            <button type="button" wire:click="$set('step', 1)"
                                class="flex items-center after:content-['/'] sm:after:hidden after:mx-2 after:text-gray-200 dark:after:text-gray-500">
                                @if ($step > 1)
                                    <span
                                        class="flex items-center justify-center w-8 h-8 rounded-full bg-green-100 dark:bg-green-800 shrink-0 me-2">
                                        <svg class="w-4 h-4 text-green-600 dark:text-green-300" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                @else
                                    <span
                                        class="flex items-center justify-center w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900 shrink-0 me-2">1</span>
                                @endif
                                <span
                                    class="inline-flex text-xs sm:text-sm whitespace-nowrap {{ $step === 1 ? 'text-primary-600 dark:text-primary-500' : 'text-gray-500 dark:text-gray-400' }}">{{ __('module_user.tab_profile') }}</span>
                            </button>
                        </li>
                        <li
                            class="flex flex-none sm:flex-1 items-center {{ $step >= 2 ? 'text-primary-600 dark:text-primary-500' : '' }} after:content-[''] after:w-full after:h-1 after:border-b after:border-gray-200 after:border-1 after:hidden sm:after:inline-block after:mx-2 xl:after:mx-4 dark:after:border-gray-700">
                            <button type="button" wire:click="$set('step', 2)"
                                class="flex items-center after:content-['/'] sm:after:hidden after:mx-2 after:text-gray-200 dark:after:text-gray-500">
                                @if ($step > 2)
                                    <span
                                        class="flex items-center justify-center w-8 h-8 rounded-full bg-green-100 dark:bg-green-800 shrink-0 me-2">
                                        <svg class="w-4 h-4 text-green-600 dark:text-green-300" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                @else
                                    <span
                                        class="flex items-center justify-center w-8 h-8 rounded-full {{ $step >= 2 ? 'bg-primary-100 dark:bg-primary-900' : 'bg-gray-100 dark:bg-gray-700' }} shrink-0 me-2">2</span>
                                @endif
                                <span
                                    class="inline-flex text-xs sm:text-sm whitespace-nowrap {{ $step === 2 ? 'text-primary-600 dark:text-primary-500' : 'text-gray-500 dark:text-gray-400' }}">{{ __('module_user.tab_access') }}</span>
                            </button>
                        </li>
                        <li
                            class="flex flex-none sm:flex-1 items-center {{ $step >= 3 ? 'text-primary-600 dark:text-primary-500' : '' }}">
                            <button type="button" wire:click="$set('step', 3)" class="flex items-center">
                                <span
                                    class="flex items-center justify-center w-8 h-8 rounded-full {{ $step >= 3 ? 'bg-primary-100 dark:bg-primary-900' : 'bg-gray-100 dark:bg-gray-700' }} shrink-0 me-2">3</span>
                                <span
                                    class="inline-flex text-xs sm:text-sm whitespace-nowrap {{ $step === 3 ? 'text-primary-600 dark:text-primary-500' : 'text-gray-500 dark:text-gray-400' }}">{{ __('module_user.tab_security') }}</span>
                            </button>
                        </li>
                    </ol>
                </div>

                <form wire:submit.prevent="save" class="p-6 pt-0 space-y-6">
                    @if ($step === 1)
                        <div class="space-y-6">
                            <div class="w-full max-w-md mx-auto mb-8">
                                <label for="avatar"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white text-center">{{ __('module_user.avatar') }}</label>
                                <p class="mb-2 text-xs text-gray-500 dark:text-gray-400 text-center">
                                    {{ __('module_user.avatar_description') }}</p>
                                <div class="flex flex-col items-center gap-4">
                                    <div class="flex-shrink-0">
                                        <div class="relative w-24 h-24">
                                            @if ($avatar)
                                                <img src="{{ $avatar->temporaryUrl() }}" alt="Avatar Preview"
                                                    class="w-24 h-24 rounded-full object-cover border-4 border-blue-300 dark:border-blue-600 shadow-sm" />
                                                <div wire:loading wire:target="avatar"
                                                    class="absolute inset-0 rounded-full bg-black bg-opacity-30 flex items-center justify-center">
                                                    <div
                                                        class="w-8 h-8 border-4 border-white border-t-transparent rounded-full animate-spin">
                                                    </div>
                                                </div>
                                            @else
                                                <div
                                                    class="w-24 h-24 rounded-full bg-gray-200 dark:bg-gray-300 border-4 border-gray-200 dark:border-gray-300 flex items-center justify-center shadow-sm">
                                                    <svg class="w-12 h-12 text-gray-400 dark:text-gray-500"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                        </path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="w-full">
                                        <input type="file" id="avatar" wire:model.live="avatar"
                                            accept="image/*"
                                            class="input-primary block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" />
                                        @error('avatar')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="fullName"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_user.full_name') }}</label>
                                    <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">
                                        {{ __('module_user.full_name_description') }}</p>
                                    <input id="fullName" type="text" wire:model.defer="fullName"
                                        placeholder="{{ __('module_user.full_name_placeholder') }}"
                                        class="input-primary shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                                    @error('fullName')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="email"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_user.email') }}</label>
                                    <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">
                                        {{ __('module_user.email_description') }}</p>
                                    <input id="email" type="email" wire:model.defer="email"
                                        placeholder="{{ __('module_user.email_placeholder') }}"
                                        class="input-primary shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div x-data="{
                                existing: {{ json_encode($existingInterests) }},
                                open: false,
                                get filtered() {
                                    const q = (($wire.newInterest || '').toString()).toLowerCase();
                                    const added = ($wire.interests || []);
                                    return this.existing.filter(i => String(i).toLowerCase().includes(q) && !added.includes(i)).slice(0, 10);
                                }
                            }" x-init="$watch('$wire.newInterest', () => {})" @click.away="open = false">
                                <label
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_user.interests') }}</label>
                                <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">
                                    {{ __('module_user.interests_description') }}</p>
                                <div class="flex flex-wrap gap-2 mb-2">
                                    @foreach ($interests as $index => $interest)
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium badge-primary dark:badge-primary-dark">
                                            {{ $interest }}
                                            <button type="button" wire:click="removeInterest({{ $index }})"
                                                class="inline-flex items-center justify-center w-4 h-4 rounded-full hover:bg-white/20 dark:hover:bg-black/20"
                                                aria-label="{{ __('module_base.remove') }}">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    @endforeach
                                </div>
                                <div class="relative flex gap-2">
                                    <div class="flex-1 relative">
                                        <input type="text" wire:model.live="newInterest"
                                            wire:keydown.enter.prevent="addInterest" @focus="open = true"
                                            @blur="setTimeout(() => open = false, 200)"
                                            placeholder="{{ __('module_user.interests_placeholder') }}"
                                            class="input-primary shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                                        <div x-show="open && filtered.length" x-transition
                                            class="absolute left-0 right-0 bottom-full mb-1 z-10 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg py-1 max-h-48 overflow-y-auto">
                                            <template x-for="item in filtered" :key="item">
                                                <button type="button"
                                                    @click="$wire.addInterestValue(item); open = false"
                                                    class="block w-full text-left px-3 py-2 text-sm text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700"
                                                    x-text="item"></button>
                                            </template>
                                        </div>
                                    </div>
                                    <button type="button" wire:click="addInterest"
                                        class="inline-flex items-center px-3 py-2 text-xs font-medium text-white rounded-lg btn-primary shrink-0">{{ __('module_base.add') }}</button>
                                </div>
                            </div>
                        </div>
                    @elseif ($step === 2)
                        <div class="space-y-6">
                            <div>
                                <label
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_user.roles') }}</label>
                                <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">
                                    {{ __('module_user.roles_description') }}</p>
                                <div
                                    class="max-h-60 overflow-y-auto border border-gray-300 rounded-lg p-4 bg-white dark:bg-gray-700 dark:border-gray-600">
                                    @if ($availableRoles->isEmpty())
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ __('module_user.no_roles_available') }}</p>
                                    @else
                                        <div class="space-y-2">
                                            @foreach ($availableRoles as $role)
                                                <label
                                                    class="flex items-center space-x-2 cursor-pointer p-2 rounded hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                                    <input type="checkbox" wire:model="roles"
                                                        value="{{ $role->id }}"
                                                        class="checkbox-primary h-4 w-4 rounded border-gray-300 bg-gray-50 focus:ring-2 focus:ring-offset-0 dark:border-gray-600 dark:bg-gray-700">
                                                    <span
                                                        class="text-sm text-gray-900 dark:text-white">{{ $role->name }}</span>
                                                    <span
                                                        class="text-xs text-gray-500 dark:text-gray-400">({{ $role->guard_name }})</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                @error('roles')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                                @error('roles.*')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            @if (config('tenancy.is_tenancy') && $isSuperAdmin)
                                <div>
                                    <label for="tenant_id"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_user.tenant') }}</label>
                                    <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">
                                        {{ __('module_user.tenant_description') }}</p>
                                    <select id="tenant_id" wire:model.defer="tenant_id"
                                        class="input-primary shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                                        <option value="">{{ __('module_user.select_tenant') }}</option>
                                        @foreach ($availableTenants as $tenant)
                                            @php
                                                $domain = $tenant->domains->first();
                                                $displayText = $domain ? $domain->domain : $tenant->id;
                                            @endphp
                                            <option value="{{ $tenant->id }}">{{ $displayText }}</option>
                                        @endforeach
                                    </select>
                                    @error('tenant_id')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="password"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_user.password') }}</label>
                                    <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">
                                        {{ __('module_user.password_description') }}</p>
                                    <div class="relative">
                                        <input id="password" type="password" wire:model.defer="password"
                                            placeholder="{{ __('module_user.password_placeholder') }}"
                                            class="input-primary shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg block w-full p-2.5 pr-10 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                                        <button type="button" data-password-toggle="password"
                                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                            <svg id="password-eye-icon" class="w-5 h-5" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7Z" />
                                            </svg>
                                            <svg id="password-eye-off-icon" class="w-5 h-5 hidden" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13.875 18.825A10.05 10.05 0 0 1 12 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 0 1 1.563-3.029m5.858.908a3 3 0 1 1 4.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0 1 12 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 0 1-4.132 5.411m0 0L21 21" />
                                            </svg>
                                        </button>
                                    </div>
                                    @error('password')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="password_confirmation"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_user.confirm_password') }}</label>
                                    <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">
                                        {{ __('module_user.confirm_password_description') }}</p>
                                    <div class="relative">
                                        <input id="password_confirmation" type="password"
                                            wire:model.defer="password_confirmation"
                                            placeholder="{{ __('module_user.password_confirmation_placeholder') }}"
                                            class="input-primary shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg block w-full p-2.5 pr-10 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                                        <button type="button" data-password-toggle="password_confirmation"
                                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                            <svg id="password_confirmation-eye-icon" class="w-5 h-5" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7Z" />
                                            </svg>
                                            <svg id="password_confirmation-eye-off-icon" class="w-5 h-5 hidden"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13.875 18.825A10.05 10.05 0 0 1 12 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 0 1 1.563-3.029m5.858.908a3 3 0 1 1 4.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0 1 12 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 0 1-4.132 5.411m0 0L21 21" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            @if ($step > 1)
                                <button type="button" wire:click="previousStep"
                                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700">{{ __('module_base.previous') }}</button>
                            @endif
                            @if ($step < 3)
                                <button type="button" wire:click="nextStep"
                                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white rounded-lg btn-primary">{{ __('module_base.next') }}</button>
                            @endif
                        </div>
                        <div class="flex items-center gap-3">
                            @can(\Src\V1\Api\User\Enums\PermissionEnum::USER_VIEW->value)
                                <a href="{{ tenant_routes('admin.users.index') }}"
                                    class="inline-flex items-center px-2 py-1 text-xs font-medium text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700">{{ __('module_base.cancel') }}</a>
                            @endcan
                            @if ($step === 3)
                                <button type="submit"
                                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-center text-white rounded-lg btn-primary">{{ __('common.save') }}</button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
    @include('components.footer')
</div>

@push('styles')
    <link href="{{ asset('css/module/user.css') }}" rel="stylesheet" />
@endpush

@push('scripts-end')
    <script src="{{ asset('js/module/user.js') }}"></script>
@endpush
