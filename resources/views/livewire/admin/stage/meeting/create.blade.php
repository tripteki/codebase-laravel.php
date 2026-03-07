<div class="min-h-screen flex flex-col bg-gray-100 lg:pl-64 dark:bg-gray-900" data-sidebar-content>
    @include('components.header')

    <main id="main-content" class="flex-1 px-4 py-6 lg:px-6">
        @php
            $StageMeetingPermissionEnum = App\Enum\Stage\StageMeetingPermissionEnum::class;
        @endphp
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
                        @can($StageMeetingPermissionEnum::STAGE_MEETING_VIEW->value)
                            <li>
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-gray-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M7.293 14.707a1 1 0 0 1 0-1.414L10.586 10 7.293 6.707a1 1 0 0 1 1.414-1.414l4 4a1 1 0 0 1 0 1.414l-4 4a1 1 0 0 1-1.414 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <a href="{{ tenant_routes('admin.stage.meetings.index') }}"
                                        class="inline-flex items-center ml-1 text-gray-700 hover:text-primary-600 md:ml-2 dark:text-gray-300 dark:hover:text-white">
                                        <svg class="w-4 h-4 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd"
                                                d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        {{ __('module_stage.meeting_title') }}
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
                    {{ __('module_stage.create_meeting') }}</h1>
            </div>

            <div class="bg-white rounded-lg shadow dark:bg-gray-800">
                <form wire:submit.prevent="save" class="p-6 space-y-6">
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6">
                            <label for="title"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_stage.column_title') }}</label>
                            <input id="title" type="text" wire:model.defer="title"
                                placeholder="{{ __('module_stage.title_placeholder') }}"
                                class="input-primary shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-span-6">
                            <label for="description"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_stage.column_description') }}</label>
                            <div wire:ignore
                                class="wysiwyg-container w-full bg-gray-50 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600"
                                data-wysiwyg-editor="description-editor" data-wysiwyg-input="description-input">
                                <div class="p-2 border-b border-gray-300 dark:border-gray-600">
                                    <div class="flex flex-wrap items-center gap-1 rtl:space-x-reverse">
                                        <button type="button" data-wysiwyg-action="bold"
                                            class="p-1.5 text-gray-700 rounded-sm cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-600"
                                            title="Bold">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 5h4.5a3.5 3.5 0 1 1 0 7H8m0-7v7m0-7H6m2 7h6.5a3.5 3.5 0 1 1 0 7H8m0-7v7m0 0H6" />
                                            </svg>
                                            <span class="sr-only">Bold</span>
                                        </button>
                                        <button type="button" data-wysiwyg-action="italic"
                                            class="p-1.5 text-gray-700 rounded-sm cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-600"
                                            title="Italic">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10 4h4m-2 2v12m-2 2h4M6 20h4M6 4h4" />
                                            </svg>
                                            <span class="sr-only">Italic</span>
                                        </button>
                                        <button type="button" data-wysiwyg-action="underline"
                                            class="p-1.5 text-gray-700 rounded-sm cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-600"
                                            title="Underline">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 4v8a5 5 0 0010 0V4M5 20h14" />
                                            </svg>
                                            <span class="sr-only">Underline</span>
                                        </button>
                                        <button type="button" data-wysiwyg-action="code"
                                            class="p-1.5 text-gray-700 rounded-sm cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-600"
                                            title="Code">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                                            </svg>
                                            <span class="sr-only">Code</span>
                                        </button>
                                        <div class="w-px h-6 bg-gray-300 dark:bg-gray-600 mx-1"></div>
                                        <button type="button" data-wysiwyg-action="alignLeft"
                                            class="p-1.5 text-gray-700 rounded-sm cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-600"
                                            title="Align Left">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 6h18M3 12h18M3 18h18" />
                                            </svg>
                                            <span class="sr-only">Align Left</span>
                                        </button>
                                        <button type="button" data-wysiwyg-action="alignCenter"
                                            class="p-1.5 text-gray-700 rounded-sm cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-600"
                                            title="Align Center">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 6h18M6 12h12M3 18h18" />
                                            </svg>
                                            <span class="sr-only">Align Center</span>
                                        </button>
                                        <button type="button" data-wysiwyg-action="alignRight"
                                            class="p-1.5 text-gray-700 rounded-sm cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-600"
                                            title="Align Right">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 6h18M9 12h12M7 18h14" />
                                            </svg>
                                            <span class="sr-only">Align Right</span>
                                        </button>
                                        <div class="w-px h-6 bg-gray-300 dark:bg-gray-600 mx-1"></div>
                                        <button type="button" data-wysiwyg-action="blockquote"
                                            class="p-1.5 text-gray-700 rounded-sm cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-600"
                                            title="Blockquote">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span class="sr-only">Blockquote</span>
                                        </button>
                                        <button type="button" data-wysiwyg-action="link"
                                            class="p-1.5 text-gray-700 rounded-sm cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-600"
                                            title="Link">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                            </svg>
                                            <span class="sr-only">Link</span>
                                        </button>
                                    </div>
                                </div>
                                <div id="description-editor" class="min-h-[200px]"></div>
                                <input type="hidden" id="description-input" wire:model="description">
                            </div>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-span-6">
                            <label
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_stage.attachments') }}</label>
                            <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">
                                {{ __('module_stage.attachments_hint') }}</p>
                            <div class="flex flex-wrap items-center gap-2">
                                <input id="create-attachments-input" type="file" wire:model="newAttachmentInput"
                                    multiple class="hidden">
                                <label for="create-attachments-input"
                                    class="inline-flex cursor-pointer items-center justify-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                                    <span class="mr-1.5 text-lg leading-none">+</span>
                                    {{ __('module_stage.attachment_add') }}
                                </label>
                            </div>
                            @error('attachments.*')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            @error('newAttachmentInput.*')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            @if (count($attachments) > 0)
                                <div class="mt-2 flex gap-3 overflow-x-auto pb-3">
                                    @foreach ($attachments as $i => $file)
                                        <figure class="relative shrink-0">
                                            <div class="relative">
                                                @if ($file->isPreviewable())
                                                    <a href="{{ $file->temporaryUrl() }}" target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="block rounded-lg border border-gray-200 bg-gray-50 hover:border-primary-500 hover:bg-primary-50 dark:border-gray-600 dark:bg-gray-700 dark:hover:border-primary-500 dark:hover:bg-primary-900/20 transition-colors overflow-hidden">
                                                        @if (str_starts_with($file->getMimeType() ?? '', 'image/'))
                                                            <img src="{{ $file->temporaryUrl() }}"
                                                                alt="{{ $file->getClientOriginalName() }}"
                                                                class="h-auto rounded-lg object-cover"
                                                                style="width: 120px; height: 120px;">
                                                        @else
                                                            <div class="flex items-center justify-center rounded-lg bg-gray-200 dark:bg-gray-600 text-gray-500 dark:text-gray-400"
                                                                style="width: 120px; height: 120px;">
                                                                <svg class="h-8 w-8" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                                </svg>
                                                            </div>
                                                        @endif
                                                    </a>
                                                @else
                                                    <div
                                                        class="block rounded-lg border border-gray-200 bg-gray-50 dark:border-gray-600 dark:bg-gray-700 overflow-hidden">
                                                        <div class="flex items-center justify-center rounded-lg bg-gray-200 dark:bg-gray-600 text-gray-500 dark:text-gray-400"
                                                            style="width: 120px; height: 120px;">
                                                            <svg class="h-8 w-8" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                            </svg>
                                                        </div>
                                                    </div>
                                                @endif
                                                <button type="button"
                                                    wire:click="removeNewAttachment({{ $i }})"
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
                                                style="width: 120px;">{{ $file->getClientOriginalName() }}
                                            </figcaption>
                                        </figure>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="col-span-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                            @if (!($hideDelegateSelect ?? false))
                                <div class="min-w-0">
                                    <label
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_stage.delegates') }}</label>
                                    <div class="relative" x-data="{
                                        open: false,
                                        query: '',
                                        get selected() {
                                            const id = $wire.delegate_id;
                                            if (!id) return null;
                                            return {{ json_encode(
                                                $delegateUsers->map(
                                                        fn($u) => [
                                                            'id' => (string) $u->id,
                                                            'name' => (string) $u->name,
                                                            'email' => (string) $u->email,
                                                            'avatar' => $u->profile?->avatar ? asset('storage/' . $u->profile->avatar) : null,
                                                        ],
                                                    )->values()->toArray(),
                                            ) }}.find(u => u.id === String(id)) || null;
                                        },
                                        get items() {
                                            const q = (this.query || '').toLowerCase();
                                            const list = {{ json_encode(
                                                $delegateUsers->map(
                                                        fn($u) => [
                                                            'id' => (string) $u->id,
                                                            'name' => (string) $u->name,
                                                            'email' => (string) $u->email,
                                                            'avatar' => $u->profile?->avatar ? asset('storage/' . $u->profile->avatar) : null,
                                                        ],
                                                    )->values()->toArray(),
                                            ) }};
                                            if (!q) return list.slice(0, 10);
                                            return list.filter(u => (u.name + ' ' + u.email).toLowerCase().includes(q)).slice(0, 10);
                                        }
                                    }" @click.away="open = false">
                                        <button type="button" @click="open = !open"
                                            class="w-full flex items-center justify-between gap-3 input-primary shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <template x-if="selected && selected.avatar">
                                                    <img :src="selected.avatar" alt=""
                                                        class="h-10 w-10 shrink-0 rounded-full object-cover shadow-sm">
                                                </template>
                                                <template x-if="selected && !selected.avatar">
                                                    <div
                                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-200 dark:bg-gray-600">
                                                        <svg class="h-5 w-5 text-gray-400 dark:text-gray-500"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                        </svg>
                                                    </div>
                                                </template>
                                                <template x-if="selected">
                                                    <div class="min-w-0">
                                                        <div class="truncate text-sm font-medium"
                                                            x-text="selected.name"></div>
                                                        <div class="truncate text-xs text-gray-500 dark:text-gray-400"
                                                            x-text="selected.email"></div>
                                                    </div>
                                                </template>
                                                <template x-if="!selected">
                                                    <span
                                                        class="text-sm text-gray-500 dark:text-gray-400">{{ __('module_stage.no_delegate') }}</span>
                                                </template>
                                            </div>
                                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 shrink-0"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>

                                        <div x-show="open" x-transition
                                            class="absolute z-20 bottom-full mb-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-600 dark:bg-gray-800">
                                            <div class="p-2 border-b border-gray-200 dark:border-gray-600">
                                                <input type="text" x-model="query"
                                                    class="input-primary block w-full px-3 py-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                                    placeholder="{{ __('common.search') }}">
                                            </div>
                                            <div class="max-h-60 overflow-y-auto py-1">
                                                <template x-for="u in items" :key="u.id">
                                                    <button type="button"
                                                        @click="$wire.set('delegate_id', u.id); open = false; query = ''"
                                                        class="w-full text-left px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                        <div class="flex items-center gap-3">
                                                            <template x-if="u.avatar">
                                                                <img :src="u.avatar" alt=""
                                                                    class="h-8 w-8 shrink-0 rounded-full object-cover shadow-sm">
                                                            </template>
                                                            <template x-if="!u.avatar">
                                                                <div
                                                                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-200 dark:bg-gray-600">
                                                                    <svg class="h-4 w-4 text-gray-400 dark:text-gray-500"
                                                                        fill="none" stroke="currentColor"
                                                                        viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                                    </svg>
                                                                </div>
                                                            </template>
                                                            <div class="min-w-0">
                                                                <div class="truncate text-sm font-medium text-gray-900 dark:text-white"
                                                                    x-text="u.name"></div>
                                                                <div class="truncate text-xs text-gray-500 dark:text-gray-400"
                                                                    x-text="u.email"></div>
                                                            </div>
                                                        </div>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    @error('delegate_id')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif
                            <div class="min-w-0 {{ $hideDelegateSelect ?? false ? 'md:col-span-2' : '' }}">
                                <label
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_stage.exhibitors_sponsors') }}</label>
                                <div x-data="{
                                    query: '',
                                    selectedInterest: '',
                                }" class="space-y-2">
                                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                        <input type="text" x-model="query"
                                            class="input-primary block w-full px-3 py-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                            placeholder="{{ __('module_stage.filter_exhibitors_sponsors_placeholder') }}">
                                        <select x-model="selectedInterest"
                                            class="input-primary block w-full px-3 py-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <option value="">
                                                {{ __('module_stage.filter_exhibitors_sponsors_all_interests') }}
                                            </option>
                                            @foreach ($availableInterests as $interest)
                                                <option value="{{ $interest }}">{{ $interest }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div x-ref="exhibitorList"
                                        x-show="!query || Array.from($refs.exhibitorList.querySelectorAll('label')).some(el => getComputedStyle(el).display !== 'none')"
                                        class="max-h-36 overflow-y-auto rounded-lg border border-gray-200 bg-gray-50 p-2 dark:border-gray-600 dark:bg-gray-700">
                                        @forelse($exhibitorSponsorUsers as $user)
                                            @php
                                                $interestValues = $user->profile?->interests ?? [];
                                                if (!is_array($interestValues)) {
                                                    $interestValues = [];
                                                }
                                                $interestText = implode(', ', $interestValues);
                                                $searchText = strtolower(
                                                    trim($user->name . ' ' . $user->email . ' ' . $interestText),
                                                );
                                            @endphp
                                            <label
                                                class="flex items-center gap-2 py-1.5 text-sm text-gray-700 dark:text-gray-300"
                                                x-data="{ searchText: {{ json_encode($searchText, JSON_UNESCAPED_UNICODE) }} }"
                                                x-show="(!query || searchText.includes(query.toLowerCase())) && (!selectedInterest || searchText.includes(selectedInterest.toLowerCase()))">
                                                @if ($user->profile?->avatar)
                                                    <img src="{{ asset('storage/' . $user->profile->avatar) }}"
                                                        alt=""
                                                        class="h-8 w-8 shrink-0 rounded-full object-cover shadow-sm">
                                                @else
                                                    <div
                                                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-200 dark:bg-gray-600">
                                                        <svg class="h-4 w-4 text-gray-400 dark:text-gray-500"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                        </svg>
                                                    </div>
                                                @endif
                                                <input type="checkbox" wire:model="exhibitor_sponsor_ids"
                                                    value="{{ $user->id }}"
                                                    class="checkbox-primary rounded border-gray-300 bg-gray-50 focus:ring-2 focus:ring-offset-0 dark:border-gray-500 dark:bg-gray-600">
                                                <span class="truncate">{{ $user->name }}</span>
                                                <span
                                                    class="shrink-0 text-gray-500 dark:text-gray-400">({{ $user->email }})</span>
                                            </label>
                                        @empty
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ __('module_stage.no_exhibitors_sponsors') }}</p>
                                        @endforelse
                                    </div>
                                </div>
                                @error('exhibitor_sponsor_ids.*')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-span-6">
                            <div class="space-y-4">
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <label for="startDate"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_stage.meeting_start_date') }}</label>
                                        @if ($eventStartDate && $eventEndDate)
                                            <p class="mb-1 text-xs text-gray-500 dark:text-gray-400">
                                                {{ __('module_stage.date_range_hint', ['min' => $eventStartDate, 'max' => $eventEndDate]) }}
                                            </p>
                                        @elseif ($eventStartDate)
                                            <p class="mb-1 text-xs text-gray-500 dark:text-gray-400">
                                                {{ __('module_stage.date_range_hint_from', ['min' => $eventStartDate]) }}
                                            </p>
                                        @elseif ($eventEndDate)
                                            <p class="mb-1 text-xs text-gray-500 dark:text-gray-400">
                                                {{ __('module_stage.date_range_hint_until', ['max' => $eventEndDate]) }}
                                            </p>
                                        @endif
                                        @include('components.admin.date', [
                                            'id' => 'startDate',
                                            'wireModel' => 'startDate',
                                            'value' => $startDate ?? '',
                                            'minDate' => $eventStartDate ?? '',
                                            'maxDate' => $eventEndDate ?? '',
                                        ])
                                        @error('startDate')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="startTime"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_stage.meeting_start_time') }}</label>
                                        @include('components.admin.time', [
                                            'id' => 'startTime',
                                            'wireModel' => 'startTime',
                                            'value' => $startTime,
                                            'defaultHour' => '09',
                                            'defaultMinute' => '00',
                                            'defaultPeriod' => 'AM',
                                        ])
                                        @error('startTime')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <label for="endDate"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_stage.meeting_end_date') }}</label>
                                        @php
                                            $endMin = $startDate ?? $eventStartDate;
                                        @endphp

                                        @if ($endMin && $eventEndDate)
                                            <p class="mb-1 text-xs text-gray-500 dark:text-gray-400">
                                                {{ __('module_stage.date_range_hint', ['min' => $endMin, 'max' => $eventEndDate]) }}
                                            </p>
                                        @elseif ($endMin)
                                            <p class="mb-1 text-xs text-gray-500 dark:text-gray-400">
                                                {{ __('module_stage.date_range_hint_from', ['min' => $endMin]) }}</p>
                                        @elseif ($eventEndDate)
                                            <p class="mb-1 text-xs text-gray-500 dark:text-gray-400">
                                                {{ __('module_stage.date_range_hint_until', ['max' => $eventEndDate]) }}
                                            </p>
                                        @endif
                                        @include('components.admin.date', [
                                            'id' => 'endDate',
                                            'wireModel' => 'endDate',
                                            'value' => $endDate ?? '',
                                            'minDate' => $startDate ?? ($eventStartDate ?? ''),
                                            'maxDate' => $eventEndDate ?? '',
                                        ])
                                        @error('endDate')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="endTime"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_stage.meeting_end_time') }}</label>
                                        @include('components.admin.time', [
                                            'id' => 'endTime',
                                            'wireModel' => 'endTime',
                                            'value' => $endTime,
                                            'defaultHour' => '05',
                                            'defaultMinute' => '00',
                                            'defaultPeriod' => 'PM',
                                        ])
                                        @error('endTime')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ tenant_routes('admin.stage.meetings.index') }}"
                            class="inline-flex items-center px-2 py-1 text-xs font-medium text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-700">{{ __('module_base.cancel') }}</a>
                        <button type="submit"
                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-center text-white rounded-lg btn-primary">{{ __('common.save') }}</button>
                    </div>
                </form>
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

@push('scripts-start')
    <script type="module">
        import {
            Editor,
        } from "https://esm.sh/@tiptap/core@2.1.13";
        import StarterKit from "https://esm.sh/@tiptap/starter-kit@2.1.13";
        import TextAlign from "https://esm.sh/@tiptap/extension-text-align@2.1.13";
        import Underline from "https://esm.sh/@tiptap/extension-underline@2.1.13";

        window.tiptap = {
            Editor,
        };
        window.StarterKit = StarterKit;
        window.TextAlign = TextAlign;
        window.Underline = Underline;
        window.tiptapReady = true;
    </script>
@endpush
