@if (! empty($userName) || ! empty($userEmail))
    <div class="panel panel-account mb-4">
        <p class="panel-account-title fw-semibold">{{ __('auth.email_user_information') }}</p>
        <div class="panel-account-body small">
            @if (! empty($userName))
                <div class="info-row">
                    <span class="info-label">{{ $userNameLabel ?? __('auth.full_name') }}</span>
                    <span class="info-value">{{ $userName }}</span>
                </div>
            @endif
            @if (! empty($userEmail))
                <div class="info-row">
                    <span class="info-label">{{ __('auth.email_address') }}</span>
                    <span class="info-value"><span class="pill">{{ $userEmail }}</span></span>
                </div>
            @endif
        </div>
    </div>
@endif
