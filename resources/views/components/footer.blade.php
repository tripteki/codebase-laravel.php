@php
    $SettingHelper = App\Helpers\SettingHelper::class;
@endphp

<footer class="mt-auto">
    <div class="container mx-auto px-4 py-4">
        <p class="text-center text-sm text-muted-foreground">
            {{ $SettingHelper::get('ALL_RIGHTS_RESERVED') }}
        </p>
    </div>
</footer>
