<div class="min-h-screen flex flex-col bg-gray-100 lg:pl-64 dark:bg-gray-900" data-sidebar-content>
    @include('components.header')
    @php
        $StageSessionPermissionEnum = App\Enum\Stage\StageSessionPermissionEnum::class;
    @endphp

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
                        @can($StageSessionPermissionEnum::STAGE_SESSION_VIEW->value)
                            <li>
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-gray-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M7.293 14.707a1 1 0 0 1 0-1.414L10.586 10 7.293 6.707a1 1 0 0 1 1.414-1.414l4 4a1 1 0 0 1 0 1.414l-4 4a1 1 0 0 1-1.414 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <a href="{{ tenant_routes('admin.stage.sessions.index') }}"
                                        class="inline-flex items-center ml-1 text-gray-700 hover:text-primary-600 md:ml-2 dark:text-gray-300 dark:hover:text-white">
                                        <svg class="w-4 h-4 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        {{ __('module_stage.session_title') }}
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
                                    <svg class="w-4 h-4 mr-2 shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ __('module_base.view') }}
                                </span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">
                    {{ __('module_stage.view_session') }}</h1>
            </div>

            <div class="bg-white rounded-lg shadow dark:bg-gray-800">
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6 sm:col-span-3">
                            <label
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_stage.column_id') }}</label>
                            <div class="bg-gray-50 text-gray-900 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:text-white"
                                title="{{ $session->id }}">{{ $session->room_id ?? '-' }}</div>
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <label
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_stage.column_title') }}</label>
                            <div
                                class="bg-gray-50 text-gray-900 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:text-white">
                                {{ $session->title }}</div>
                        </div>
                        <div class="col-span-6">
                            <label
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_stage.column_description') }}</label>
                            <div
                                class="prose prose-sm max-w-none bg-gray-50 text-gray-900 sm:text-sm rounded-lg block w-full p-4 dark:bg-gray-700 dark:text-white dark:prose-invert">
                                {!! $session->description ?? '<p>-</p>' !!}
                            </div>
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <label
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_stage.column_start_at') }}</label>
                            <div
                                class="bg-gray-50 text-gray-900 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:text-white">
                                {{ $session->start_at?->format('Y-m-d g:i A') ?? '-' }}</div>
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <label
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_stage.column_end_at') }}</label>
                            <div
                                class="bg-gray-50 text-gray-900 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:text-white">
                                {{ $session->end_at?->format('Y-m-d g:i A') ?? '-' }}</div>
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <label
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_base.column_created_at') }}</label>
                            <div
                                class="bg-gray-50 text-gray-900 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:text-white">
                                {{ $session->created_at?->format('Y-m-d H:i:s') ?? '-' }}</div>
                        </div>
                        @if ($session->speakers->isNotEmpty())
                            <div class="col-span-6">
                                <label
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_stage.speakers') }}</label>
                                <div
                                    class="space-y-2 rounded-lg border border-gray-200 bg-gray-50 p-2.5 dark:border-gray-600 dark:bg-gray-700">
                                    @foreach ($session->speakers as $invitation)
                                        @php
                                            $speakerUser = $invitation->user;
                                        @endphp
                                        @if ($speakerUser)
                                            <div class="flex items-center gap-3">
                                                @if ($speakerUser->profile?->avatar)
                                                    <img src="{{ asset('storage/' . $speakerUser->profile->avatar) }}"
                                                        alt=""
                                                        class="h-10 w-10 shrink-0 rounded-full object-cover shadow-sm">
                                                @else
                                                    <div
                                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-200 dark:bg-gray-600">
                                                        <svg class="h-5 w-5 text-gray-400 dark:text-gray-500"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                        </svg>
                                                    </div>
                                                @endif
                                                <div>
                                                    <a href="{{ tenant_routes('admin.users.show', $speakerUser) }}"
                                                        class="inline-flex items-center gap-1 text-sm font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">
                                                        <span
                                                            class="text-gray-900 dark:text-white">{{ $speakerUser->name }}</span>
                                                        <span
                                                            class="text-gray-500 dark:text-gray-400">({{ $speakerUser->email }})</span>
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        @if ($session->attachments->isNotEmpty())
                            <div class="col-span-6">
                                <label
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_stage.attachments') }}</label>
                                <div class="flex gap-3 overflow-x-auto pb-3">
                                    @foreach ($session->attachments as $att)
                                        <figure class="relative shrink-0">
                                            <div class="relative">
                                                <a href="{{ $att->url() }}" target="_blank" rel="noopener noreferrer"
                                                    class="block rounded-lg border border-gray-200 bg-gray-50 hover:border-primary-500 hover:bg-primary-50 dark:border-gray-600 dark:bg-gray-700 dark:hover:border-primary-500 dark:hover:bg-primary-900/20 transition-colors overflow-hidden">
                                                    @if ($att->isImage())
                                                        <img src="{{ $att->url() }}"
                                                            alt="{{ $att->original_name }}"
                                                            class="h-auto rounded-lg object-cover"
                                                            style="width: 120px; height: 120px;">
                                                    @else
                                                        <div class="flex items-center justify-center rounded-lg bg-gray-200 dark:bg-gray-600 text-gray-500 dark:text-gray-400"
                                                            style="width: 120px; height: 120px;">
                                                            <svg class="h-8 w-8" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                            </svg>
                                                        </div>
                                                    @endif
                                                </a>
                                                <button type="button"
                                                    wire:click="removeAttachment('{{ $att->id }}')"
                                                    wire:confirm="{{ __('module_stage.attachment_remove_confirm') }}"
                                                    class="absolute top-1 right-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-white hover:bg-red-600 dark:bg-red-600 dark:hover:bg-red-700 transition-colors shadow-sm"
                                                    title="{{ __('module_stage.attachment_remove') }}">
                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <figcaption
                                                class="mt-1 text-xs text-center text-gray-600 dark:text-gray-300 truncate"
                                                style="width: 120px;">{{ $att->original_name }}</figcaption>
                                        </figure>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                        @can($StageSessionPermissionEnum::STAGE_SESSION_VIEW->value)
                            <a href="{{ tenant_routes('admin.stage.sessions.index') }}"
                                class="inline-flex items-center px-2 py-1 text-xs font-medium text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-700">{{ __('module_base.back_to_list') }}</a>
                        @else
                            <span></span>
                        @endcan
                        @can($StageSessionPermissionEnum::STAGE_SESSION_UPDATE->value, $session)
                            <a href="{{ tenant_routes('admin.stage.sessions.edit', $session) }}"
                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-center text-white rounded-lg btn-tertiary">{{ __('module_stage.edit_session') }}</a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </main>
    @include('components.footer')
</div>

@push('styles')
    <link href="{{ asset('css/module/stage.css') }}" rel="stylesheet" />
@endpush

@push('scripts-end')
    <script src="{{ asset('js/module/stage.js') }}"></script>
@endpush
