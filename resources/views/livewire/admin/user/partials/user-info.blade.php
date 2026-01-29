<div class="flex items-center">
    <div class="flex-shrink-0 h-10 w-10">
        <div class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
            <span class="text-sm font-medium text-gray-600 dark:text-gray-300">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </span>
        </div>
    </div>
    <div class="ml-4">
        <div class="text-sm font-medium text-gray-900 dark:text-white">
            {{ $user->name }}
        </div>
        <div class="text-sm text-gray-500 dark:text-gray-400">
            {{ $user->email }}
        </div>
    </div>
</div>
