@extends("layouts.app")

@section("title", __('auth.forgot_password'))

@section("content")
    <div class="min-h-screen flex flex-col bg-gray-50 dark:bg-gray-950">
        @include("components.header", [ "showLogout" => false, ])

        <main class="flex flex-1 items-center justify-center px-4">
            <div class="w-full max-w-md space-y-6">
            <div class="text-center space-y-2">
                <h1 class="text-2xl font-bold tracking-tight">
                    {{ __('auth.forgot_password_title') }}
                </h1>
                <p class="text-sm text-muted-foreground">
                    {{ __('auth.forgot_password_description') }}
                </p>
            </div>

            @if (session("status"))
                <div class="rounded-md bg-green-50 p-3 text-sm text-green-700 text-center">
                    {{ session("status") }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.password.email') }}" class="space-y-4">
                @csrf

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
                        autofocus
                        autocomplete="email"
                        placeholder="{{ __('auth.email_placeholder') }}"
                        class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                    />
                    @error("email")
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="inline-flex w-full items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:bg-blue-500 dark:hover:bg-blue-400"
                >
                    {{ __('auth.email_password_reset_link') }}
                </button>
            </form>

            <div class="text-center text-sm text-muted-foreground">
                <span>{{ __('auth.or_return_to') }}</span>
                <a
                    href="{{ route('admin.login') }}"
                    class="inline-flex items-center gap-1 font-medium text-primary hover:underline"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-3">
                        <path fill-rule="evenodd" d="M12.5 9.75A2.75 2.75 0 0 0 9.75 7H4.56l2.22 2.22a.75.75 0 1 1-1.06 1.06l-3.5-3.5a.75.75 0 0 1 0-1.06l3.5-3.5a.75.75 0 0 1 1.06 1.06L4.56 5.5h5.19a4.25 4.25 0 0 1 0 8.5h-1a.75.75 0 0 1 0-1.5h1a2.75 2.75 0 0 0 2.75-2.75Z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ __('auth.log_in_lower') }}</span>
                </a>
            </div>
        </main>

        @include("components.footer")
    </div>
@endsection
