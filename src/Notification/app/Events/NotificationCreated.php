<?php

namespace Modules\Notification\App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param string $userId
     * @param string $notificationId
     * @param int $unread
     * @return void
     */
    public function __construct(
        public string $userId,
        public string $notificationId,
        public int $unread,
    ) {}

    /**
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [ new PrivateChannel("user.".$this->userId), ];
    }

    /**
     * @return string
     */
    public function broadcastAs(): string
    {
        return "v1.notification.created";
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            "id" => $this->notificationId,
            "unread" => $this->unread,
        ];
    }
}
