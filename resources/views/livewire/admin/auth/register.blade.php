@extends("layouts.app")

@section("title", __('auth.register'))

@section("content")
    <div class="min-h-screen flex flex-col bg-gray-50 dark:bg-gray-950">
        @include("components.header", [ "showLogout" => false, ])

        <main class="flex flex-1 items-center justify-center px-4">
            <div class="w-full max-w-md space-y-6">
            <div class="text-center space-y-2">
                <h1 class="text-2xl font-bold tracking-tight">
                    {{ __('auth.register_title') }}
                </h1>
                <p class="text-sm text-muted-foreground">
                    {{ __('auth.register_description') }}
                </p>
            </div>

            <form method="POST" action="{{ route('admin.register') }}" class="space-y-4">
                @csrf

                <div class="space-y-2">
                    <label for="name" class="block text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ __('auth.name') }}
                    </label>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old("name") }}"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="{{ __('auth.username') }}"
                        class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                    />
                    @error("name")
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="email" class="block text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ __('auth.email_address') }}
                    </label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old("email") }}"
                        required
                        autocomplete="email"
                        placeholder="{{ __('auth.email_placeholder') }}"
                        class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                    />
                    @error("email")
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="password" class="block text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ __('auth.password') }}
                    </label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="new-password"
                        placeholder="{{ __('auth.password_placeholder') }}"
                        class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                    />
                    @error("password")
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ __('auth.password_confirmation_label') }}
                    </label>
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="{{ __('auth.password_confirmation_placeholder') }}"
                        class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                    />
                    @error("password_confirmation")
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="inline-flex w-full items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:bg-blue-500 dark:hover:bg-blue-400"
                >
                    {{ __('auth.create_account') }}
                </button>
            </form>

            <div class="text-center text-sm text-muted-foreground">
                {{ __('auth.already_have_account') }}
                <a href="{{ route('admin.login') }}" class="font-medium text-primary hover:underline">
                    {{ __('auth.log_in') }}
                </a>
            </div>
        </main>

        @include("components.footer")
    </div>
@endsection
