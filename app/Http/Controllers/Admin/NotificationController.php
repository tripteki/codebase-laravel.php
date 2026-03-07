<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    /**
     * @param string $id
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function readAndRedirect(string $id, Request $request): RedirectResponse
    {
        $notification = auth()->user()->notifications()->find($id);

        if ($notification && is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        $url = $request->query('url');
        $fallback = tenant_routes('admin.notifications.index');

        if (empty($url)) {
            return redirect()->to($fallback);
        }

        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $allowedHost = $request->getHost();
            $targetHost = parse_url($url, PHP_URL_HOST);
            if ($targetHost !== $allowedHost) {
                return redirect()->to($fallback);
            }
        } else {
            if (! str_starts_with($url, '/')) {
                return redirect()->to($fallback);
            }
        }

        return redirect()->to($url);
    }

    /**
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markRead(string $id): RedirectResponse
    {
        $notification = auth()->user()->notifications()->find($id);

        if ($notification && is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return redirect()->to(tenant_routes('admin.notifications.index'));
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAllAsRead(): RedirectResponse
    {
        auth()->user()->unreadNotifications->markAsRead();

        return redirect()->to(tenant_routes('admin.notifications.index'));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getData(): JsonResponse
    {
        try {
            $unreadCount = auth()->user()->unreadNotifications()->count();
            $notifications = auth()->user()->notifications()->take(10)->get()->map(function ($notification) {
                $isRead = !is_null($notification->read_at);
                $notificationData = $notification->data ?? [];
                $title = $notificationData['title'] ?? __('common.notification');
                $body = $notificationData['body'] ?? '';

                return [
                    'id' => $notification->id,
                    'title' => $title,
                    'body' => $body,
                    'is_read' => $isRead,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'mark_read_url' => tenant_routes('admin.notifications.mark-read', $notification->id),
                ];
            });

            return response()->json([
                'unread_count' => $unreadCount,
                'notifications' => $notifications->toArray(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'unread_count' => 0,
                'notifications' => [],
            ]);
        }
    }
}
