@props([
    'editorId',
    'inputId',
    'model',
    'minHeight' => '200px',
])

<div wire:ignore
    class="wysiwyg-container w-full bg-gray-50 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600"
    data-wysiwyg-editor="{{ $editorId }}"
    data-wysiwyg-input="{{ $inputId }}">
    <div class="p-2 border-b border-gray-300 dark:border-gray-600">
        <div class="flex flex-wrap items-center gap-1 rtl:space-x-reverse">
            <button type="button" data-wysiwyg-action="bold"
                class="p-1.5 text-gray-700 rounded-sm cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-600"
                title="Bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5h4.5a3.5 3.5 0 1 1 0 7H8m0-7v7m0-7H6m2 7h6.5a3.5 3.5 0 1 1 0 7H8m0-7v7m0 0H6" />
                </svg>
                <span class="sr-only">Bold</span>
            </button>
            <button type="button" data-wysiwyg-action="italic"
                class="p-1.5 text-gray-700 rounded-sm cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-600"
                title="Italic">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 4h4m-2 2v12m-2 2h4M6 20h4M6 4h4" />
                </svg>
                <span class="sr-only">Italic</span>
            </button>
            <button type="button" data-wysiwyg-action="underline"
                class="p-1.5 text-gray-700 rounded-sm cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-600"
                title="Underline">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v8a5 5 0 0010 0V4M5 20h14" />
                </svg>
                <span class="sr-only">Underline</span>
            </button>
            <button type="button" data-wysiwyg-action="code"
                class="p-1.5 text-gray-700 rounded-sm cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-600"
                title="Code">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                </svg>
                <span class="sr-only">Code</span>
            </button>
            <div class="w-px h-6 bg-gray-300 dark:bg-gray-600 mx-1"></div>
            <button type="button" data-wysiwyg-action="alignLeft"
                class="p-1.5 text-gray-700 rounded-sm cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-600"
                title="Align Left">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6h18M3 12h18M3 18h18" />
                </svg>
                <span class="sr-only">Align Left</span>
            </button>
            <button type="button" data-wysiwyg-action="alignCenter"
                class="p-1.5 text-gray-700 rounded-sm cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-600"
                title="Align Center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6h18M6 12h12M3 18h18" />
                </svg>
                <span class="sr-only">Align Center</span>
            </button>
            <button type="button" data-wysiwyg-action="alignRight"
                class="p-1.5 text-gray-700 rounded-sm cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-600"
                title="Align Right">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6h18M9 12h12M7 18h14" />
                </svg>
                <span class="sr-only">Align Right</span>
            </button>
            <div class="w-px h-6 bg-gray-300 dark:bg-gray-600 mx-1"></div>
            <button type="button" data-wysiwyg-action="blockquote"
                class="p-1.5 text-gray-700 rounded-sm cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-600"
                title="Blockquote">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="sr-only">Blockquote</span>
            </button>
            <button type="button" data-wysiwyg-action="link"
                class="p-1.5 text-gray-700 rounded-sm cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-600"
                title="Link">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                </svg>
                <span class="sr-only">Link</span>
            </button>
        </div>
    </div>

    <div id="{{ $editorId }}" style="min-height: {{ $minHeight }};"></div>
    <input type="hidden" id="{{ $inputId }}" wire:model="{{ $model }}">
</div>
