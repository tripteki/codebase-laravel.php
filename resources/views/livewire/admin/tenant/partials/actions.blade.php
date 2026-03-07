<div class="flex items-center space-x-1.5">
    @php
        $TenantPermissionEnum = App\Enum\Tenant\PermissionEnum::class;
    @endphp
    @can($TenantPermissionEnum::EVENT_UPDATE->value)
        <button type="button"
            wire:click="$dispatchTo('admin.tenant.content-index-component', 'openLocaleModal', { locale: '{{ $code }}' })"
            class="inline-flex items-center px-2 py-1 text-xs font-medium text-center text-white rounded-lg btn-tertiary">
            <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path d="M17.414 2.586a2 2 0 0 0-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 0 0 0-2.828Z" />
                <path fill-rule="evenodd"
                    d="M2 6a2 2 0 0 1 2-2h4a1 1 0 1 1 0 2H4v10h10v-4a 1 1 0 1 1 2 0v4a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6Z"
                    clip-rule="evenodd" />
            </svg>
            {{ __('module_base.edit') }}
        </button>
    @endcan
</div>
