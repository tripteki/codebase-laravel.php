@push('styles')
<link href="{{ asset('css/components/notification.css') }}" rel="stylesheet" />
@endpush

@push('scripts-end')
<script src="{{ asset('js/components/notification.js') }}"></script>
@endpush

@auth
    <livewire:admin.notification.notification-component />
@endauth
