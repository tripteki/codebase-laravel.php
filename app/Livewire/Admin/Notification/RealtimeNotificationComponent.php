<?php

namespace App\Livewire\Admin\Notification;

use Livewire\Component;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class RealtimeNotificationComponent extends Component
{
    /**
     * @var string|null
     */
    public ?string $lastNotificationId = null;

    /**
     * @var string|null
     */
    public ?string $lastNotificationCreatedAt = null;

    /**
     * @var array<string>
     */
    public array $notificationTypes = [];

    /**
     * @var int
     */
    public int $pollInterval = 5;

    /**
     * @param array<string> $notificationTypes
     * @param int $pollInterval
     * @return void
     */
    public function mount(array $notificationTypes = [], int $pollInterval = 5): void
    {
        $this->notificationTypes = $notificationTypes;
        $this->pollInterval = $pollInterval;

        $query = Auth::user()->notifications()->latest();

        if (! empty($this->notificationTypes)) {
            $query->whereIn('type', $this->notificationTypes);
        }

        $latestNotification = $query->first();

        if ($latestNotification) {
            $this->lastNotificationId = $latestNotification->id;
            $this->lastNotificationCreatedAt = $latestNotification->created_at->toDateTimeString();
        }
    }

    /**
     * @return void
     */
    public function checkNotifications(): void
    {
        $query = Auth::user()->notifications()->latest();

        if (! empty($this->notificationTypes)) {
            $query->whereIn('type', $this->notificationTypes);
        }

        if ($this->lastNotificationCreatedAt) {
            $query->where('created_at', '>', $this->lastNotificationCreatedAt);
        }

        $newNotifications = $query->limit(10)->get();

        if ($newNotifications->isNotEmpty()) {
            $latestNotification = $newNotifications->first();
            $this->lastNotificationId = $latestNotification->id;
            $this->lastNotificationCreatedAt = $latestNotification->created_at->toDateTimeString();
        }

        foreach ($newNotifications as $notification) {
            $data = $notification->data ?? [];
            $title = $data['title'] ?? '';
            $body = $data['body'] ?? '';

            $variant = null;
            $readAndOpenUrl = null;
            $linkText = null;
            $iconKey = $data['presentation_icon'] ?? $data['icon'] ?? 'default';
            $theme = self::resolveToastTheme($data);
            $icon = $iconKey;

            if (isset($data['url'])) {
                $url = $data['url'];
                $isAbsoluteUrl = str_starts_with($url, 'http://') || str_starts_with($url, 'https://');
                $redirectTo = $isAbsoluteUrl ? $url : asset('storage/' . $url);

                $pathForExt = parse_url($redirectTo, PHP_URL_PATH) ?? $url;
                $ext = strtolower(pathinfo($pathForExt, PATHINFO_EXTENSION));
                $linkText = ($ext !== '' && preg_match('/^[a-z0-9]{2,6}$/', $ext)) ? __('common.download') : __('common.visit');
                $readAndOpenUrl = tenant_routes('admin.notifications.read-and-redirect', ['id' => $notification->id]) . '?url=' . rawurlencode($redirectTo);
                $variant = self::linkToastVariantFromNotificationData($data);
            }

            if ($variant !== null && $readAndOpenUrl !== null) {
                $this->dispatch('toast',
                    variant: $variant,
                    title: $title ?: __('common.notification'),
                    message: $body,
                    readAndOpenUrl: $readAndOpenUrl,
                    linkText: $linkText,
                    id: $notification->id,
                    theme: $theme,
                    icon: $icon,
                );
            } elseif ($title || $body) {
                $message = $title && $body ? "{$title}: {$body}" : ($title ?: $body);
                $this->dispatch('toast', message: $message, type: 'success', id: $notification->id);
            }

            if (! empty($data['refresh_datatables'])) {
                $this->dispatch('refreshDatatable');
            }
        }
    }

    /**
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        return view('livewire.admin.notification.realtime-notification');
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function linkToastVariantFromNotificationData(array $data): string
    {
        $icon = $data['presentation_icon'] ?? $data['icon'] ?? null;

        if (! is_string($icon)) {
            return 'default';
        }

        $normalized = strtolower(str_replace('_', '-', trim($icon)));

        if ($normalized !== '' && preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $normalized)) {
            return $normalized;
        }

        return 'default';
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function resolveToastTheme(array $data): string
    {
        if (isset($data['toast_theme']) && in_array($data['toast_theme'], ['success', 'danger'], true)) {
            return $data['toast_theme'];
        }

        if ((int) ($data['failed_rows'] ?? 0) > 0) {
            return 'danger';
        }

        $status = $data['status'] ?? null;

        if (in_array($status, ['danger', 'warning'], true)) {
            return 'danger';
        }

        return 'success';
    }
}
