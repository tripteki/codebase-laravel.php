<?php

namespace App\Livewire\Admin\Notification;

use Livewire\Component;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class RealtimeNotificationComponent extends Component
{
    /**
     * Last checked notification ID.
     *
     * @var string|null
     */
    public ?string $lastNotificationId = null;

    /**
     * Last checked notification created_at timestamp.
     *
     * @var string|null
     */
    public ?string $lastNotificationCreatedAt = null;

    /**
     * Notification types to check (empty array means all types).
     * Example: ['App\\Notifications\\ImportCompletedNotification']
     *
     * @var array<string>
     */
    public array $notificationTypes = [];

    /**
     * Polling interval in seconds.
     * Default: 5 seconds
     *
     * @var int
     */
    public int $pollInterval = 5;

    /**
     * Mount the component.
     *
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
     * Check for new notifications.
     *
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

            $toastType = $this->getToastType($notification->type);

            $message = '';
            if ($title && $body) {
                $message = "{$title}: {$body}";
            } elseif ($title) {
                $message = $title;
            } elseif ($body) {
                $message = $body;
            }

            if ($message) {
                $this->dispatch('toast', message: $message, type: $toastType, id: $notification->id);
            }
        }
    }

    /**
     * Get toast type based on notification type.
     *
     * @param string $notificationType
     * @return string
     */
    protected function getToastType(string $notificationType): string
    {
        $typeMap = [
            'App\\Notifications\\ImportCompletedNotification' => 'success',
            'App\\Notifications\\ExportCompletedNotification' => 'success',
        ];

        return $typeMap[$notificationType] ?? 'success';
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        return view('livewire.admin.notification.realtime-notification');
    }
}
