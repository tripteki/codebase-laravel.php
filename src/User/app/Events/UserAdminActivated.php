<?php

namespace Modules\User\App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\User\App\Dtos\UserTransformerDto;

class UserAdminActivated implements ShouldBroadcast
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
        return "v1.user.admin.activated";
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return self::userPayload($this->user);
    }

    /**
     * @param \App\Models\User $user
     * @return array<string, mixed>
     */
    public static function userPayload(User $user): array
    {
        $dto = UserTransformerDto::fromUser($user);

        return [
            "id" => $dto->id,
            "name" => $dto->name,
            "email" => $dto->email,
            "email_verified_at" => $dto->email_verified_at?->format("c"),
            "created_at" => $dto->created_at?->format("c"),
            "updated_at" => $dto->updated_at?->format("c"),
            "deleted_at" => $dto->deleted_at?->format("c"),
        ];
    }
}
