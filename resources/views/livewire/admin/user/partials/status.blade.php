<div class="flex items-center">
    @if ($verified)
        <span class="h-2.5 w-2.5 rounded-full bg-green-400 mr-2"></span>
        <span class="text-gray-900 dark:text-gray-200">{{ __("module_user.verified") }}</span>
    @else
        <span class="h-2.5 w-2.5 rounded-full bg-red-500 mr-2"></span>
        <span class="text-gray-900 dark:text-gray-200">{{ __("module_user.unverified") }}</span>
    @endif
</div>
