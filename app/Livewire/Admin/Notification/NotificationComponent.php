<?php

namespace App\Livewire\Admin\Notification;

use Livewire\Component;
use Illuminate\View\View;

class NotificationComponent extends Component
{
    /**
     * @var string
     */
    public string $activeTab = 'all';

    /**
     * @var string
     */
    public string $notificationsIndexUrl = '';

    public function mount(): void
    {
        $this->notificationsIndexUrl = tenant_routes('admin.notifications.index');
    }

    /**
     * @param string $tab
     * @return void
     */
    public function setTab(string $tab): void
    {
        if (in_array($tab, ['all', 'unread', 'read'])) {
            $this->activeTab = $tab;
        }

        $this->dispatch('notification-tab-changed');
    }

    /**
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
     * @return void
     */
    public function markAllAsRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();
    }

    /**
     * @return void
     */
    public function refresh(): void
    {
        //
    }

    /**
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        try {
            $unreadCount = auth()->user()->unreadNotifications()->count();

            $query = auth()->user()->notifications()->latest();

            if ($this->activeTab === 'unread') {
                $query->whereNull('read_at');
            } elseif ($this->activeTab === 'read') {
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
