<div class="flex items-center">
    @if ($verified)
        <span class="h-2.5 w-2.5 rounded-full bg-[hsl(89,75%,54.5%)] mr-2"></span>
        <span class="text-gray-900 dark:text-gray-200">{{ __('module_user.verified') }}</span>
    @else
        <span class="h-2.5 w-2.5 rounded-full bg-[hsl(0,84.2%,60.2%)] mr-2"></span>
        <span class="text-gray-900 dark:text-gray-200">{{ __('module_user.unverified') }}</span>
    @endif
</div>
