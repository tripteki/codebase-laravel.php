<?php

namespace Modules\Log\App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminRefresh implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param string $userId
     * @param list<string> $scopes
     * @return void
     */
    public function __construct(
        public string $userId,
        public array $scopes,
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
        return "v1.admin.refresh";
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            "userId" => $this->userId,
            "scopes" => array_values($this->scopes),
        ];
    }
}
