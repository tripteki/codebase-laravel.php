<?php

namespace App\Livewire\Admin\Notification;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\View\View;

class NotificationIndexComponent extends Component
{
    use WithPagination;

    /**
     * @var string
     */
    public string $activeTab = 'all';

    /**
     * @param string $tab
     * @return void
     */
    public function mount(string $tab = "all"): void
    {
        if (in_array($tab, ["all", "unread", "read"], true)) {
            $this->activeTab = $tab;
        }
    }

    /**
     * @param string $tab
     * @return void
     */
    public function setTab(string $tab): void
    {
        if (in_array($tab, ["all", "unread", "read"], true)) {
            redirect()->to(tenant_routes("admin.notifications.tab", ["tab" => $tab]));
        }
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

        session()->flash('message', __('common.notification_marked_as_read'));
    }

    /**
     * @return void
     */
    public function markAllAsRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();

        session()->flash('message', __('common.all_notifications_marked_as_read'));
    }

    /**
     * @param string $notificationId
     * @return void
     */
    public function delete(string $notificationId): void
    {
        $notification = auth()->user()->notifications()->find($notificationId);

        if ($notification) {
            $notification->delete();
        }

        session()->flash('message', __('common.notification_deleted'));
    }

    /**
     * @return void
     */
    public function deleteAll(): void
    {
        auth()->user()->notifications()->delete();

        session()->flash('message', __('common.all_notifications_deleted'));
    }

    /**
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        $query = auth()->user()->notifications();

        if ($this->activeTab === 'unread') {
            $query->whereNull('read_at');
        } elseif ($this->activeTab === 'read') {
            $query->whereNotNull('read_at');
        }

        $notifications = $query->paginate(10);

        return view('livewire.admin.notification.index', [
            'notifications' => $notifications,
        ])->layout('layouts.app', [
            'title' => __('common.notifications'),
            'showSidebar' => true,
        ]);
    }
}
