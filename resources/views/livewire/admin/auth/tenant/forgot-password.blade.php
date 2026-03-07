@extends('layouts.tenant.auth')

@section('title', __('auth.forgot_password'))

@section('auth_right')
    <div
        class="w-full lg:w-1/2 relative overflow-hidden flex items-center justify-center px-4 sm:px-6 lg:px-8 py-8 sm:py-12 lg:py-0 text-white auth-hero-bg min-h-screen lg:min-h-0">
        <div
            class="absolute inset-0 opacity-30 pointer-events-none bg-[radial-gradient(rgba(255,255,255,0.26)_1px,transparent_1px)] dark:bg-[radial-gradient(rgba(255,255,255,0.18)_1px,transparent_1px)] bg-[length:22px_22px]">
        </div>

        <div class="relative z-10 w-full max-w-md flex flex-col justify-center min-h-full py-8">
            <div class="text-center space-y-2 mb-6">
                <h1 class="text-2xl font-bold tracking-tight">
                    {{ __('auth.forgot_password_title') }}
                </h1>
                <p class="text-sm text-white/80">
                    {{ __('auth.forgot_password_description') }}
                </p>
            </div>

            @if (session('status'))
                <div
                    class="rounded-full bg-[hsl(89,75%,54.5%)]/20 border border-[hsl(89,75%,54.5%)]/40 px-4 py-3 text-xs sm:text-sm text-white text-center mb-4">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ tenant_routes('admin.password.email') }}" class="space-y-4">
                @csrf

                <div class="space-y-2">
                    <label for="email" class="block text-sm font-medium text-white">
                        {{ __('auth.email_address') }}
                    </label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        autocomplete="email" placeholder="{{ __('auth.email_placeholder') }}"
                        class="block w-full rounded-full border border-transparent bg-white px-4 py-2.5 text-sm text-gray-900 shadow-[inset_0_1px_0_rgba(0,0,0,0.03),0_10px_24px_rgba(0,0,0,0.10)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--tenant-primary)]" />
                    @error('email')
                        <p class="mt-1 text-sm text-[hsl(0,84.2%,60.2%)]">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="inline-flex w-full items-center justify-center rounded-full px-4 py-2.5 text-xs sm:text-sm font-semibold shadow focus-visible:outline-none transition-all btn-primary">
                    {{ __('auth.email_password_reset_link') }}
                </button>
            </form>

            <div class="mt-4 text-center text-xs sm:text-sm text-white/80">
                <span>{{ __('auth.or_return_to') }}</span>
                <a href="{{ tenant_routes('admin.login') }}"
                    class="inline-flex items-center gap-1 font-medium hover:underline link-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="h-3 w-3">
                        <path fill-rule="evenodd"
                            d="M12.5 9.75A2.75 2.75 0 0 0 9.75 7H4.56l2.22 2.22a.75.75 0 1 1-1.06 1.06l-3.5-3.5a.75.75 0 0 1 0-1.06l3.5-3.5a.75.75 0 1 1 1.06 1.06L4.56 5.5h5.19a4.25 4.25 0 0 1 0 8.5h-1a.75.75 0 0 1 0-1.5h1a2.75 2.75 0 0 0 2.75-2.75Z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>{{ __('auth.log_in_lower') }}</span>
                </a>
            </div>

        </div>
    </div>
@endsection
