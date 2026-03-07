@php
    $user = auth()->user();
    $initials = strtoupper(substr($user->name ?? 'U', 0, 1));
    $avatarUrl = $user->profile?->avatar ? asset('storage/' . $user->profile->avatar) : asset('asset/avatar.png');
    $primaryRole = $user->roles->first()?->name ?? '';
@endphp

<div class="relative">
    <button
        type="button"
        data-dropdown-toggle="user-dropdown"
        class="inline-flex items-center gap-2 rounded-full px-2 py-1 bg-gray-100 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 transition-colors"
        aria-expanded="false"
    >
        <div class="flex h-8 w-8 items-center justify-center rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700">
            @if ($avatarUrl && $avatarUrl !== asset('asset/avatar.png'))
                <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" class="w-full h-full object-cover" />
            @else
                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $initials }}</span>
            @endif
        </div>

        <div class="hidden xl:flex flex-col items-start leading-tight">
            <span class="text-xs font-semibold text-gray-900 dark:text-white truncate" style="max-width:220px">
                {{ $user->name }}
            </span>
            <span class="text-[0.7rem] text-gray-500 dark:text-gray-400 truncate" style="max-width:220px">
                {{ $user->email }}
            </span>
            @if ($primaryRole)
                <span class="text-[0.7rem] text-gray-500 dark:text-gray-400 capitalize">
                    {{ $primaryRole }}
                </span>
            @endif
        </div>

        <svg class="hidden xl:inline h-3 w-3 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
            <path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/>
        </svg>
    </button>

    <div
        id="user-dropdown"
        class="absolute right-0 top-12 z-50 hidden w-56 divide-y divide-gray-100 rounded-lg border border-gray-200 dark:border-gray-600 bg-white text-sm shadow-lg dark:divide-gray-600 dark:bg-gray-800"
    >
        <div class="block xl:hidden px-3 py-2 border-b border-gray-200 dark:border-gray-600">
            <div class="flex items-center gap-2">
                <div class="flex h-8 w-8 items-center justify-center rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700">
                    @if ($avatarUrl && $avatarUrl !== asset('asset/avatar.png'))
                        <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" class="w-full h-full object-cover" />
                    @else
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $initials }}</span>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-semibold text-gray-900 dark:text-white truncate" style="max-width:160px">
                        {{ $user->name }}
                    </div>
                    <div class="text-[0.7rem] text-gray-500 dark:text-gray-400 truncate" style="max-width:160px">
                        {{ $user->email }}
                    </div>
                    @if ($primaryRole)
                        <div class="text-[0.7rem] text-gray-500 dark:text-gray-400 capitalize">
                            {{ $primaryRole }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <ul class="py-2">
            <li>
                <a href="{{ tenant_routes('admin.settings.tab', ['tab' => 'personal']) }}" class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700 transition-colors">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    {{ __('common.profiles') }}
                </a>
            </li>
            @if (! hasTenant())
                <li>
                    <a href="{{ tenant_routes('admin.settings.tab', ['tab' => 'system']) }}" class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700 transition-colors">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        {{ __('common.settings') }}
                    </a>
                </li>
            @endif
        </ul>

        <div class="py-2">
            <form method="POST" action="{{ tenant_routes('admin.logout') }}">
                @csrf
                <button
                    type="submit"
                    class="flex w-full items-center gap-2 px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20 transition-colors"
                >
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    {{ __('auth.logout') }}
                </button>
            </form>
        </div>
    </div>
</div>
