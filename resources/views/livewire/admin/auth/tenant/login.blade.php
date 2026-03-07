@extends('layouts.tenant.auth')

@section('title', __('auth.login'))

@section('auth_right')
    @php
        use App\Models\Setting;

        $authWelcomeMessage = hasTenant()
            ? tenant_trans('auth.welcome_back_message')
            : ((trim(
                (string) (Setting::query()
                    ->whereNull('tenant_id')
                    ->where('key', 'CONTENT_AUTH_WELCOME_BACK_MESSAGE')
                    ->value('value') ?? ''),
            )) ?: __('auth.welcome_back_message'));
        $defaultLogoPng = asset('asset/logo.png');
        $logoSrc =
            config('tenancy.is_tenancy') && hasTenant() && tenant('icon')
                ? asset('storage/' . tenant('icon'))
                : $defaultLogoPng;
        $loginWordmarkDotted = implode(
            '.',
            array_map(
                static fn (string $c): string => mb_strtoupper($c, 'UTF-8'),
                mb_str_split((string) preg_replace('/\s+/u', '', config('app.name')), 1, 'UTF-8'),
            ),
        );
        $loginWordmarkDisplay = $loginWordmarkDotted;
    @endphp
    <div
        class="w-full lg:w-1/2 relative overflow-hidden flex items-center justify-center px-4 sm:px-6 lg:px-8 py-8 sm:py-12 lg:py-0 text-white auth-hero-bg min-h-screen lg:min-h-0">
        <div
            class="absolute inset-0 opacity-30 pointer-events-none bg-[radial-gradient(rgba(255,255,255,0.26)_1px,transparent_1px)] dark:bg-[radial-gradient(rgba(255,255,255,0.18)_1px,transparent_1px)] bg-[length:22px_22px]">
        </div>

        <div class="relative z-10 w-full max-w-md lg:max-w-[560px] flex flex-col justify-center min-h-full py-8 lg:py-0">
            <div class="hidden lg:block space-y-6">
                <div class="flex justify-center items-center gap-4 mb-16 lg:mb-24 min-h-[56px]">
                    <img src="{{ $logoSrc }}" alt="{{ config('app.name') }}"
                        class="auth-logo h-16 lg:h-20 xl:h-24 object-contain" />
                    <span
                        class="auth-text text-4xl lg:text-5xl xl:text-6xl 2xl:text-7xl font-extrabold tracking-tight drop-shadow-sm">
                        {{ mb_strtoupper(config('app.name'), 'UTF-8') }}
                    </span>
                </div>

                <div class="w-full">
                    <h4 class="text-center mb-6 font-semibold text-white text-xl">{{ $authWelcomeMessage }}</h4>

                    @if (session('status'))
                        <div
                            class="rounded-full bg-[hsl(89,75%,54.5%)]/20 border border-[hsl(89,75%,54.5%)]/40 px-4 py-3 text-sm text-white text-center mb-4">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ tenant_routes('admin.login') }}" class="space-y-4" autocomplete="off">
                        @csrf

                        <div class="space-y-1">
                            <input type="email" name="identifier" value="{{ old('identifier') }}"
                                class="block w-full rounded-full border border-transparent bg-white px-4 py-3 sm:py-3.5 text-sm text-gray-900 shadow-[inset_0_1px_0_rgba(0,0,0,0.03),0_10px_24px_rgba(0,0,0,0.10)] focus-visible:outline-none focus-visible:border-[#FCB11D] focus-visible:ring-3 focus-visible:ring-[rgba(252,177,29,0.1)] placeholder-gray-500 transition-all @error('email') ring-2 ring-[hsl(0,84.2%,60.2%)] @enderror @error('identifier') ring-2 ring-[hsl(0,84.2%,60.2%)] @enderror"
                                placeholder="{{ __('auth.email_placeholder') }}" autocomplete="email" autocapitalize="none"
                                autocorrect="off" inputmode="email" spellcheck="false" required>
                            @error('email')
                                <p class="mt-1.5 text-sm text-white/90">{{ $message }}</p>
                            @enderror
                            @error('identifier')
                                <p class="mt-1.5 text-sm text-white/90">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1">
                            <div class="relative">
                                <input type="password" id="loginPassword" name="password"
                                    class="block w-full rounded-full border border-transparent bg-white px-4 py-3 sm:py-3.5 pr-12 text-sm text-gray-900 shadow-[inset_0_1px_0_rgba(0,0,0,0.03),0_10px_24px_rgba(0,0,0,0.10)] focus-visible:outline-none focus-visible:border-[#FCB11D] focus-visible:ring-3 focus-visible:ring-[rgba(252,177,29,0.1)] placeholder-gray-500 transition-all @error('password') ring-2 ring-[hsl(0,84.2%,60.2%)] @enderror"
                                    placeholder="{{ __('auth.password_placeholder') }}" autocomplete="current-password"
                                    autocapitalize="none" autocorrect="off" spellcheck="false" required>
                                <button
                                    class="absolute right-3 -bottom-1/2 -translate-y-1/2 bg-[#FCB11D] text-white rounded-full p-2 hover:bg-[#e5a017] focus:outline-none focus:ring-2 focus:ring-[#FCB11D]/50 transition-colors active:scale-95"
                                    type="button" data-password-toggle="loginPassword"
                                    aria-label="{{ __('auth.show_password') }}">
                                    <svg id="loginPassword-eye-icon" class="w-4 h-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                    <svg id="loginPassword-eye-off-icon" class="w-4 h-4 hidden" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.29 3.29m0 0A9.97 9.97 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-1.5 text-sm text-white/90">{{ $message }}</p>
                            @enderror
                        </div>

                        <div
                            class="flex items-center mb-4 {{ hasTenant() ? 'justify-between' : 'justify-start' }}">
                            <div class="flex items-center space-x-2.5">
                                <input type="hidden" name="remember" value="0">
                                <input
                                    class="checkbox-primary h-4 w-4 rounded border-white/40 bg-white/10 cursor-pointer transition-colors focus:ring-2 focus:ring-offset-0"
                                    type="checkbox" id="rememberMe" name="remember" value="1"
                                    {{ old('remember') ? 'checked' : '' }}>
                                <label class="text-sm text-white/80 cursor-pointer select-none" for="rememberMe">
                                    {{ __('auth.remember_me') }}
                                </label>
                            </div>
                            @if (hasTenant())
                                <a href="{{ tenant_routes('admin.password.request') }}"
                                    class="text-xs sm:text-sm text-white/80 hover:text-white underline decoration-white/40 hover:decoration-white transition-colors">
                                    {{ __('auth.forgot_password_link') }}
                                </a>
                            @endif
                        </div>

                        <button type="submit"
                            class="w-full rounded-full px-4 py-3.5 text-sm font-semibold shadow-[0_4px_12px_rgba(0,0,0,0.15)] transition-all hover:shadow-[0_6px_16px_rgba(0,0,0,0.2)] focus:outline-none active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed btn-primary">
                            {{ __('auth.continue') }}
                        </button>
                    </form>

                    @if (hasTenant())
                        <div class="mt-4 text-sm text-white/70 text-center">
                            {{ __('auth.dont_have_account') }}
                            <a href="{{ tenant_routes('admin.register') }}"
                                class="underline transition-colors link-primary">
                                {{ __('auth.create_account') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="lg:hidden w-full space-y-5">
                <div class="flex justify-center items-center gap-3 mb-6">
                    <img src="{{ $logoSrc }}" alt="{{ config('app.name') }}"
                        class="auth-logo h-16 sm:h-18 object-contain" />
                    <span class="auth-text text-4xl sm:text-5xl font-extrabold tracking-tight drop-shadow-sm">
                        {{ $loginWordmarkDisplay }}
                    </span>
                </div>
                <div class="w-full">
                    <h4
                        class="auth-welcome-mobile text-center mb-5 sm:mb-6 font-semibold text-white text-base sm:text-lg leading-tight px-2">
                        {{ $authWelcomeMessage }}</h4>

                    @if (session('status'))
                        <div
                            class="rounded-full bg-[hsl(89,75%,54.5%)]/20 border border-[hsl(89,75%,54.5%)]/40 px-4 py-2.5 sm:py-3 text-xs sm:text-sm text-white text-center mb-4">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ tenant_routes('admin.login') }}" class="space-y-3.5 sm:space-y-4"
                        autocomplete="off">
                        @csrf

                        <div class="space-y-1">
                            <input type="email" name="identifier" value="{{ old('identifier') }}"
                                class="block w-full rounded-full border border-transparent bg-white px-4 py-2.5 sm:py-3 text-sm text-gray-900 shadow-[inset_0_1px_0_rgba(0,0,0,0.03),0_10px_24px_rgba(0,0,0,0.10)] focus-visible:outline-none focus-visible:border-[#FCB11D] focus-visible:ring-3 focus-visible:ring-[rgba(252,177,29,0.1)] placeholder-gray-500 transition-all @error('email') ring-2 ring-[hsl(0,84.2%,60.2%)] @enderror @error('identifier') ring-2 ring-[hsl(0,84.2%,60.2%)] @enderror"
                                placeholder="{{ __('auth.email_placeholder') }}" autocomplete="email"
                                autocapitalize="none" autocorrect="off" inputmode="email" spellcheck="false" required>
                            @error('email')
                                <p class="mt-1.5 text-xs sm:text-sm text-white/90">{{ $message }}</p>
                            @enderror
                            @error('identifier')
                                <p class="mt-1.5 text-xs sm:text-sm text-white/90">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1">
                            <div class="relative">
                                <input type="password" id="mLoginPassword" name="password"
                                    class="block w-full rounded-full border border-transparent bg-white px-4 py-2.5 sm:py-3 pr-12 text-sm text-gray-900 shadow-[inset_0_1px_0_rgba(0,0,0,0.03),0_10px_24px_rgba(0,0,0,0.10)] focus-visible:outline-none focus-visible:border-[#FCB11D] focus-visible:ring-3 focus-visible:ring-[rgba(252,177,29,0.1)] placeholder-gray-500 transition-all @error('password') ring-2 ring-[hsl(0,84.2%,60.2%)] @enderror"
                                    placeholder="{{ __('auth.password_placeholder') }}" autocomplete="current-password"
                                    autocapitalize="none" autocorrect="off" spellcheck="false" required>
                                <button
                                    class="absolute right-3 -bottom-1/2 -translate-y-1/2 bg-[#FCB11D] text-white rounded-full p-1.5 sm:p-2 hover:bg-[#e5a017] focus:outline-none focus:ring-2 focus:ring-[#FCB11D]/50 transition-colors active:scale-95"
                                    type="button" data-password-toggle="mLoginPassword"
                                    aria-label="{{ __('auth.show_password') }}">
                                    <svg id="mLoginPassword-eye-icon" class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                    <svg id="mLoginPassword-eye-off-icon" class="w-3.5 h-3.5 sm:w-4 sm:h-4 hidden"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.29 3.29m0 0A9.97 9.97 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-1.5 text-xs sm:text-sm text-white/90">{{ $message }}</p>
                            @enderror
                        </div>

                        <div
                            class="flex items-center mb-3 sm:mb-4 {{ hasTenant() ? 'justify-between' : 'justify-start' }}">
                            <div class="flex items-center space-x-2">
                                <input type="hidden" name="remember" value="0">
                                <input
                                    class="checkbox-primary h-3.5 w-3.5 sm:h-4 sm:w-4 rounded border-white/40 bg-white/10 cursor-pointer transition-colors focus:ring-2 focus:ring-offset-0"
                                    type="checkbox" id="rememberMeMobile" name="remember" value="1"
                                    {{ old('remember') ? 'checked' : '' }}>
                                <label class="text-xs sm:text-sm text-white/80 cursor-pointer select-none"
                                    for="rememberMeMobile">
                                    {{ __('auth.remember_me') }}
                                </label>
                            </div>
                            @if (hasTenant())
                                <a href="{{ tenant_routes('admin.password.request') }}"
                                    class="text-xs sm:text-sm text-white/80 hover:text-white underline decoration-white/40 hover:decoration-white transition-colors">
                                    {{ __('auth.forgot_password_link') }}
                                </a>
                            @endif
                        </div>

                        <button type="submit"
                            class="w-full rounded-full px-4 py-3 sm:py-3.5 text-xs sm:text-sm font-semibold shadow-[0_4px_12px_rgba(0,0,0,0.15)] transition-all hover:shadow-[0_6px_16px_rgba(0,0,0,0.2)] focus:outline-none active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed btn-primary">
                            {{ __('auth.continue') }}
                        </button>
                    </form>

                    @if (hasTenant())
                        <div class="mt-4 text-xs sm:text-sm text-white/70 text-center">
                            {{ __('auth.dont_have_account') }}
                            <a href="{{ tenant_routes('admin.register') }}"
                                class="underline transition-colors link-primary">
                                {{ __('auth.create_account') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection
