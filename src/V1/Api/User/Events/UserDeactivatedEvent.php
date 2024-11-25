<?php

namespace Src\V1\Api\User\Events;

use Src\V1\Api\User\Dtos\UserDto;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;

class UserDeactivatedEvent implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var \Src\V1\Api\User\Dtos\UserDto
     */
    public \Src\V1\Api\User\Dtos\UserDto $user;

    /**
     * @param \Src\V1\Api\User\Dtos\UserDto $user
     * @return void
     */
    public function __construct(UserDto $user)
    {
        $this->user = $user;
    }

    /**
     * @return array
     */
    public function broadcastWith(): array
    {
        return [

            "user" => $this->user,
        ];
    }

    /**
     * @return \Illuminate\Broadcasting\PrivateChannel
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("v1.user.".$this->user->id);
    }

    /**
     * @return string
     */
    public function broadcastAs(): string
    {
        return "user.deactivated";
    }
}
