@php
    $tone = $tone ?? 'primary';
@endphp

<div class="email-hero email-hero--{{ $tone }} mb-4">
    <div class="email-hero__icon" aria-hidden="true">{{ $icon ?? '✉' }}</div>
    <div class="email-hero__copy">
        <h1 class="email-hero__title mb-0">{{ $title }}</h1>
        @if (! empty($subtitle))
            <p class="email-hero__subtitle mb-0">{{ $subtitle }}</p>
        @endif
    </div>
</div>
