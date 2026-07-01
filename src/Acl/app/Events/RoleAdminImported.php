<?php

namespace Modules\Acl\App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RoleAdminImported implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $userId,
        public string $filename,
        public int $totalImported,
        public int $totalSkipped,
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
        return "v1.acl.role.admin.imported";
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            "userId" => $this->userId,
            "filename" => $this->filename,
            "totalImported" => $this->totalImported,
            "totalSkipped" => $this->totalSkipped,
        ];
    }
}
