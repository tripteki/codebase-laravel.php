<?php

namespace Modules\Event\App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventAdminImportedFailed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param string $userId
     * @param string $filename
     * @param string $message
     * @param string $error
     * @return void
     */
    public function __construct(
        public string $userId,
        public string $filename,
        public string $message,
        public string $error,
    ) {}

    /**
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [new PrivateChannel("user.".$this->userId)];
    }

    /**
     * @return string
     */
    public function broadcastAs(): string
    {
        return "v1.event.admin.imported-failed";
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            "userId" => $this->userId,
            "filename" => $this->filename,
            "message" => $this->message,
            "error" => $this->error,
        ];
    }
}
