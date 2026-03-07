@extends('layouts.tenant.auth')

@section('title', __('auth.register'))

@section('auth_right')
    <div
        class="w-full lg:w-1/2 relative overflow-hidden flex items-center justify-center px-4 sm:px-6 lg:px-8 py-8 sm:py-12 lg:py-0 text-white auth-hero-bg min-h-screen lg:min-h-0">
        <div
            class="absolute inset-0 opacity-30 pointer-events-none bg-[radial-gradient(rgba(255,255,255,0.26)_1px,transparent_1px)] dark:bg-[radial-gradient(rgba(255,255,255,0.18)_1px,transparent_1px)] bg-[length:22px_22px]">
        </div>

        <div class="relative z-10 w-full max-w-md flex flex-col justify-center min-h-full py-8">
            <div class="text-center space-y-2 mb-6">
                <h1 class="text-2xl font-bold tracking-tight">
                    {{ __('auth.register_title') }}
                </h1>
                @if (session('success'))
                    <p class="text-sm text-white font-medium rounded-full bg-white/15 border border-white/30 px-4 py-2.5">
                        {{ session('success') }}
                    </p>
                @else
                    <p class="text-sm text-white/80">
                        {{ __('auth.register_description') }}
                    </p>
                @endif
            </div>

            <form method="POST" action="{{ tenant_routes('admin.register') }}" class="space-y-4">
                @csrf

                <div class="space-y-2">
                    <label for="full_name" class="block text-sm font-medium text-white">
                        {{ __('module_user.full_name') }}
                    </label>
                    <input id="full_name" type="text" name="full_name" value="{{ old('full_name') }}" autofocus
                        autocomplete="name" placeholder="{{ __('module_user.full_name_description') }}"
                        class="block w-full rounded-full border border-transparent bg-white px-4 py-2.5 text-sm text-gray-900 shadow-[inset_0_1px_0_rgba(0,0,0,0.03),0_10px_24px_rgba(0,0,0,0.10)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--tenant-primary)]" />
                    @error('full_name')
                        <p class="mt-1 text-sm text-[hsl(0,84.2%,60.2%)]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="email" class="block text-sm font-medium text-white">
                        {{ __('auth.email_address') }}
                    </label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                        autocomplete="email" placeholder="{{ __('auth.email_placeholder') }}"
                        class="block w-full rounded-full border border-transparent bg-white px-4 py-2.5 text-sm text-gray-900 shadow-[inset_0_1px_0_rgba(0,0,0,0.03),0_10px_24px_rgba(0,0,0,0.10)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--tenant-primary)]" />
                    @error('email')
                        <p class="mt-1 text-sm text-[hsl(0,84.2%,60.2%)]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="registerPassword" class="block text-sm font-medium text-white">
                        {{ __('auth.password') }}
                    </label>
                    <div class="relative">
                        <input id="registerPassword" type="password" name="password" required autocomplete="new-password"
                            placeholder="{{ __('auth.password_placeholder') }}"
                            class="block w-full rounded-full border border-transparent bg-white px-4 py-2.5 pr-12 text-sm text-gray-900 shadow-[inset_0_1px_0_rgba(0,0,0,0.03),0_10px_24px_rgba(0,0,0,0.10)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--tenant-primary)]" />
                        <button type="button" data-password-toggle="registerPassword"
                            aria-label="{{ __('auth.show_password') }}"
                            class="absolute right-3 -bottom-7 -translate-y-1/2 bg-[#FCB11D] text-white rounded-full p-2 hover:bg-[#e5a017] focus:outline-none focus:ring-2 focus:ring-[#FCB11D]/50 transition-colors active:scale-95">
                            <svg id="registerPassword-eye-icon" class="w-4 h-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                </path>
                            </svg>
                            <svg id="registerPassword-eye-off-icon" class="w-4 h-4 hidden" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.29 3.29m0 0A9.97 9.97 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21">
                                </path>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-[hsl(0,84.2%,60.2%)]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="registerPasswordConfirmation" class="block text-sm font-medium text-white">
                        {{ __('auth.password_confirmation_label') }}
                    </label>
                    <div class="relative">
                        <input id="registerPasswordConfirmation" type="password" name="password_confirmation" required
                            autocomplete="new-password" placeholder="{{ __('auth.password_confirmation_placeholder') }}"
                            class="block w-full rounded-full border border-transparent bg-white px-4 py-2.5 pr-12 text-sm text-gray-900 shadow-[inset_0_1px_0_rgba(0,0,0,0.03),0_10px_24px_rgba(0,0,0,0.10)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--tenant-primary)]" />
                        <button type="button" data-password-toggle="registerPasswordConfirmation"
                            aria-label="{{ __('auth.show_password') }}"
                            class="absolute right-3 -bottom-7 -translate-y-1/2 bg-[#FCB11D] text-white rounded-full p-2 hover:bg-[#e5a017] focus:outline-none focus:ring-2 focus:ring-[#FCB11D]/50 transition-colors active:scale-95">
                            <svg id="registerPasswordConfirmation-eye-icon" class="w-4 h-4" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                </path>
                            </svg>
                            <svg id="registerPasswordConfirmation-eye-off-icon" class="w-4 h-4 hidden" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.29 3.29m0 0A9.97 9.97 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21">
                                </path>
                            </svg>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <p class="mt-1 text-sm text-[hsl(0,84.2%,60.2%)]">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="inline-flex w-full items-center justify-center rounded-full px-4 py-2.5 text-xs sm:text-sm font-semibold shadow focus-visible:outline-none transition-all btn-primary">
                    {{ __('auth.create_account') }}
                </button>
            </form>

            <div class="mt-4 text-center text-xs sm:text-sm text-white/80">
                {{ __('auth.already_have_account') }}
                <a href="{{ tenant_routes('admin.login') }}" class="font-medium link-primary hover:underline">
                    {{ __('auth.log_in') }}
                </a>
            </div>

        </div>
    </div>
@endsection
