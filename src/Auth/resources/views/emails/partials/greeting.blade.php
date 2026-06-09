<p class="email-greeting fw-bold mb-4">
    @if (! empty($userName))
        {{ __('auth.email_hi') }} {{ $userName }},
    @else
        {{ __('auth.hi_there') }}
    @endif
</p>
