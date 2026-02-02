<?php

namespace App\Livewire\Admin\Notification;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\View\View;

class NotificationIndexComponent extends Component
{
    use WithPagination;

    /**
     * Active tab: 'unread' or 'read'.
     *
     * @var string
     */
    public string $activeTab = 'unread';

    /**
     * Mount the component.
     *
     * @param string|null $tab
     * @return void
     */
    public function mount(?string $tab = null): void
    {
        if ($tab && in_array($tab, ['unread', 'read'])) {
            $this->activeTab = $tab;
        }
    }

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
            $this->resetPage();
        }
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

        session()->flash('message', __('common.notification_marked_as_read'));
    }

    /**
     * Mark all notifications as read.
     *
     * @return void
     */
    public function markAllAsRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();

        session()->flash('message', __('common.all_notifications_marked_as_read'));
    }

    /**
     * Delete a notification.
     *
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
     * Delete all notifications.
     *
     * @return void
     */
    public function deleteAll(): void
    {
        auth()->user()->notifications()->delete();

        session()->flash('message', __('common.all_notifications_deleted'));
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        $query = auth()->user()->notifications();

        if ($this->activeTab === 'unread') {
            $query->whereNull('read_at');
        } else {
            $query->whereNotNull('read_at');
        }

        $notifications = $query->paginate(10);

        return view('livewire.admin.notification.index', [
            'notifications' => $notifications,
        ])->layout('layouts.app', [
            'title' => __('common.notifications'),
        ]);
    }
}
