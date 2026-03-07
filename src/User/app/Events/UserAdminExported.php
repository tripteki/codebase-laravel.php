<?php

namespace Modules\User\App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserAdminExported implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param string $userId
     * @param string $filename
     * @param string $fileUrl
     * @param string $filePath
     * @param string $message
     * @return void
     */
    public function __construct(
        public string $userId,
        public string $filename,
        public string $fileUrl,
        public string $filePath,
        public string $message,
    ) {
    }

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
        return "v1.user.admin.exported";
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
            "filePath" => $this->filePath,
        ];
    }
}
