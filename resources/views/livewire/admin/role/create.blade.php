@php
    use Src\V1\Api\Acl\Enums\GuardEnum;
@endphp

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
                                        d="M10.707 2.293a1 1 0 0 0-1.414 0l-7 7a 1 1 0 0 0 1.414 1.414L4 10.414V17a 1 1 0 0 0 1 1h2a 1 1 0 0 0 1-1v-2a 1 1 0 0 1 1-1h2a 1 1 0 0 1 1 1v2a 1 1 0 0 0 1 1h2a 1 1 0 0 0 1-1v-6.586l.293.293A1 1 0 0 0 18.707 9.293l-7-7Z" />
                                </svg>
                                {{ __('common.home') }}
                            </a>
                        </li>
                        @can(\Src\V1\Api\Acl\Enums\PermissionEnum::ROLE_VIEW->value)
                            <li>
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-gray-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M7.293 14.707a 1 1 0 0 1 0-1.414L10.586 10 7.293 6.707a 1 1 0 0 1 1.414-1.414l4 4a 1 1 0 0 1 0 1.414l-4 4a 1 1 0 0 1-1.414 0Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <a href="{{ tenant_routes('admin.roles.index') }}"
                                        class="inline-flex items-center ml-1 text-gray-700 hover:text-primary-600 md:ml-2 dark:text-gray-300 dark:hover:text-white">
                                        <svg class="w-4 h-4 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd"
                                                d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        {{ __('module_role.title') }}
                                    </a>
                                </div>
                            </li>
                        @endcan
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a 1 1 0 0 1 0-1.414L10.586 10 7.293 6.707a 1 1 0 0 1 1.414-1.414l4 4a 1 1 0 0 1 0 1.414l-4 4a 1 1 0 0 1-1.414 0Z"
                                        clip-rule="evenodd" />
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
                    {{ __('module_role.add_new_role') }}
                </h1>
            </div>

            <div class="bg-white rounded-lg shadow dark:bg-gray-800">
                <form wire:submit.prevent="save" class="p-6 space-y-6">
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6 sm:col-span-3">
                            <label for="name"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_role.name') }}</label>
                            <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">
                                {{ __('module_role.name_description') }}</p>
                            <input id="name" type="text" wire:model.defer="name"
                                placeholder="{{ __('module_role.name_placeholder') }}"
                                class="input-primary shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <label for="guard_name"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_role.guard_name') }}</label>
                            <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">
                                {{ __('module_role.guard_name_description') }}</p>
                            <select id="guard_name" wire:model.defer="guard_name"
                                class="input-primary shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                                @foreach (GuardEnum::cases() as $guard)
                                    <option value="{{ $guard->value }}">{{ $guard->value }}</option>
                                @endforeach
                            </select>
                            @error('guard_name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="col-span-6">
                        <label
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_role.permissions') }}</label>
                        <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">
                            {{ __('module_role.permissions_description') }}</p>
                        <div
                            class="max-h-60 overflow-y-auto border border-gray-300 rounded-lg p-4 bg-white dark:bg-gray-700 dark:border-gray-600">
                            @if ($availablePermissions->isEmpty())
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('module_role.no_permissions_available') }}</p>
                            @else
                                <div class="space-y-2">
                                    @foreach ($availablePermissions as $permission)
                                        <label
                                            class="flex items-center space-x-2 cursor-pointer p-2 rounded hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                            <input type="checkbox" wire:model="permissions"
                                                value="{{ $permission->id }}"
                                                class="checkbox-primary h-4 w-4 rounded border-gray-300 bg-gray-50 focus:ring-2 focus:ring-offset-0 dark:border-gray-600 dark:bg-gray-700">
                                            <span
                                                class="text-sm text-gray-900 dark:text-white">{{ $permission->name }}</span>
                                            <span
                                                class="text-xs text-gray-500 dark:text-gray-400">({{ $permission->guard_name }})</span>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        @error('permissions')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        @error('permissions.*')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        @can(\Src\V1\Api\Acl\Enums\PermissionEnum::ROLE_VIEW->value)
                            <a href="{{ tenant_routes('admin.roles.index') }}"
                                class="inline-flex items-center px-2 py-1 text-xs font-medium text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                                {{ __('module_base.cancel') }}
                            </a>
                        @endcan
                        <button type="submit"
                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-center text-white rounded-lg btn-primary">
                            {{ __('common.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    @include('components.footer')
</div>

@push('styles')
    <link href="{{ asset('css/module/role.css') }}" rel="stylesheet" />
@endpush

@push('scripts-end')
    <script src="{{ asset('js/module/role.js') }}"></script>
@endpush
