<?php

namespace Modules\User\App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserAdminDeactivated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param \App\Models\User $user
     * @param string $userId
     * @return void
     */
    public function __construct(
        public User $user,
        public string $userId,
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
        return "v1.user.admin.deactivated";
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return UserAdminActivated::userPayload($this->user);
    }
}
