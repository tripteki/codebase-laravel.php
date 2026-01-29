@php
    $user = auth()->user();
    $initials = strtoupper(substr($user->name ?? 'U', 0, 1));
    $avatarUrl = $user->profile?->avatar ? asset('storage/' . $user->profile->avatar) : null;
@endphp

<button
    type="button"
    data-dropdown-toggle="user-dropdown"
    class="flex items-center gap-2 rounded-lg px-2 py-2 text-sm text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600"
>
    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
        @if ($avatarUrl)
            <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" class="w-full h-full object-cover" />
        @else
            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $initials }}</span>
        @endif
    </div>
    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
    </svg>
</button>

<div
    id="user-dropdown"
    class="absolute border right-0 top-12 z-50 hidden w-56 divide-y divide-gray-100 rounded-lg bg-white text-sm shadow dark:divide-gray-600 dark:bg-gray-700 dark:border-gray-600"
>
    <div class="px-4 py-3">
        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
        <p class="truncate text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
    </div>
    <ul class="py-2">
        <li>
            <a href="{{ route('admin.settings.tab', ['tab' => 'personal']) }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600">
                {{ __('common.profiles') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.settings.tab', ['tab' => 'system']) }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600">
                {{ __('common.settings') }}
            </a>
        </li>
    </ul>
    <div class="py-2">
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button
                type="submit"
                class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600"
            >
                {{ __('auth.logout') }}
            </button>
        </form>
    </div>
</div>
