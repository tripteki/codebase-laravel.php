@if (! empty($url))
    <div class="url-fallback mb-4">
        <p class="small text-muted mb-2">{{ $hint ?? __('auth.verify_email_copy_url') }}</p>
        <p class="url-box small text-break mb-0">{{ $url }}</p>
    </div>
@endif
