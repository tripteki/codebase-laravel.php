<?php

namespace Modules\Acl\App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PermissionAdminExported implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $userId,
        public string $filename,
        public string $fileUrl,
        public string $filePath,
        public string $message,
    ) {}

    /**
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [new PrivateChannel("user.".$this->userId)];
    }

    public function broadcastAs(): string
    {
        return "v1.acl.permission.admin.exported";
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            "userId" => $this->userId,
            "filename" => $this->filename,
            "fileUrl" => $this->fileUrl,
        ];
    }
}
