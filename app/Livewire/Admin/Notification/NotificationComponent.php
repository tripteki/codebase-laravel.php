<?php

namespace App\Livewire\Admin\Notification;

use Livewire\Component;
use Illuminate\View\View;

class NotificationComponent extends Component
{
    /**
     * Active tab: 'unread' or 'read'.
     *
     * @var string
     */
    public string $activeTab = 'unread';

    /**
     * Set active tab.
     *
     * @param string $tab
     * @return void
     */
    public function setTab(string $tab): void
    {
        if (in_array($tab, ['unread', 'read'])) {
            $this->activeTab = $tab;
        }

        $this->dispatch('notification-tab-changed');
    }

    /**
     * Mark a notification as read.
     *
     * @param string $notificationId
     * @return void
     */
    public function markAsRead(string $notificationId): void
    {
        $notification = auth()->user()->notifications()->find($notificationId);

        if ($notification && is_null($notification->read_at)) {
            $notification->markAsRead();
        }
    }

    /**
     * Mark all notifications as read.
     *
     * @return void
     */
    public function markAllAsRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();
    }

    /**
     * Refresh notifications data.
     *
     * @return void
     */
    public function refresh(): void
    {
        //
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        try {
            $unreadCount = auth()->user()->unreadNotifications()->count();

            $query = auth()->user()->notifications()->latest();

            if ($this->activeTab === 'unread') {
                $query->whereNull('read_at');
            } else {
                $query->whereNotNull('read_at');
            }

            $notifications = $query->take(10)->get();
        } catch (\Exception $e) {
            $unreadCount = 0;
            $notifications = collect();
        }

        return view('livewire.admin.notification.notification', [
            'unreadCount' => $unreadCount,
            'notifications' => $notifications,
        ]);
    }
}
