@extends("layouts.app")

@section("title", __('auth.login'))

@section("content")
    <div class="min-h-screen flex flex-col bg-gray-50 dark:bg-gray-950">
        @include("components.header", [ "showLogout" => false, ])

        <main class="flex flex-1 items-center justify-center px-4">
            <div class="w-full max-w-md space-y-6">
            <div class="text-center space-y-2">
                <h1 class="text-2xl font-bold tracking-tight">
                    {{ __('auth.login_title') }}
                </h1>
                <p class="text-sm text-muted-foreground">
                    {{ __('auth.login_description') }}
                </p>
            </div>

            @if (session("status"))
                <div class="rounded-md bg-green-50 p-3 text-sm text-green-700 text-center">
                    {{ session("status") }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}" class="space-y-4">
                @csrf

                <div class="space-y-2">
                    <label for="identifier" class="block text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ __('auth.email_address') }}
                    </label>
                    <input
                        id="identifier"
                        type="email"
                        name="identifier"
                        value="{{ old("identifier") }}"
                        required
                        autofocus
                        autocomplete="email"
                        placeholder="{{ __('auth.email_placeholder') }}"
                        class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                    />
                    @error("email")
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error("identifier")
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ __('auth.password') }}
                        </label>
                        <a href="{{ route('admin.password.request') }}" class="text-sm font-medium text-primary hover:underline">
                            {{ __('auth.forgot_password_link') }}
                        </a>
                    </div>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="{{ __('auth.password_placeholder') }}"
                        class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                    />
                    @error("password")
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center space-x-2">
                    <input
                        id="remember"
                        type="checkbox"
                        name="remember"
                        class="h-4 w-4 rounded border-gray-300 bg-gray-300 text-blue-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:border-gray-600 dark:bg-gray-900"
                        {{ old("remember") ? "checked" : "" }}
                    />
                    <label for="remember" class="text-sm text-gray-900 dark:text-gray-100">
                        {{ __('auth.remember_me') }}
                    </label>
                </div>

                <button
                    type="submit"
                    class="inline-flex w-full items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:bg-blue-500 dark:hover:bg-blue-400"
                >
                    {{ __('auth.log_in') }}
                </button>
            </form>

            <div class="text-center text-sm text-muted-foreground">
                {{ __('auth.dont_have_account') }}
                <a href="{{ route('admin.register') }}" class="font-medium text-primary hover:underline">
                    {{ __('auth.sign_up') }}
                </a>
            </div>
        </main>

        @include("components.footer")
    </div>
@endsection
