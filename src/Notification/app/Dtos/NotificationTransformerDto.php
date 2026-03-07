<?php

namespace Modules\Notification\App\Dtos;

use Modules\Notification\App\Models\Notification;
use Modules\User\App\Dtos\UserTransformerDto;
use Carbon\Carbon;
use Spatie\LaravelData\Data;

class NotificationTransformerDto extends Data
{
    /**
     * @param string $id
     * @param string $user_id
     * @param string $type
     * @param array $data
     * @param \DateTimeInterface|null $read_at
     * @param \DateTimeInterface|null $created_at
     * @param \DateTimeInterface|null $updated_at
     * @param \DateTimeInterface|null $deleted_at
     * @param \Modules\User\App\Dtos\UserTransformerDto|null $user
     * @return void
     */
    public function __construct(
        public string $id,
        public string $user_id,
        public string $type,
        public array $data,
        public ?\DateTimeInterface $read_at,
        public ?\DateTimeInterface $created_at,
        public ?\DateTimeInterface $updated_at,
        public ?\DateTimeInterface $deleted_at = null,
        public ?UserTransformerDto $user = null,
    ) {
    }

    /**
     * @param \Modules\Notification\App\Models\Notification $notification
     * @param bool $withUser
     * @return self
     */
    public static function fromNotification(Notification $notification, bool $withUser = false): self
    {
        $user = null;

        if ($withUser && $notification->relationLoaded("notifiable") && $notification->notifiable instanceof \App\Models\User) {
            $user = UserTransformerDto::fromUser($notification->notifiable);
        }

        return new self(
            id: $notification->id,
            user_id: (string) $notification->notifiable_id,
            type: $notification->type,
            data: is_array($notification->data) ? $notification->data : (array) $notification->data,
            read_at: self::castDate($notification->read_at),
            created_at: self::castDate($notification->created_at),
            updated_at: self::castDate($notification->updated_at),
            deleted_at: self::castDate($notification->deleted_at),
            user: $user,
        );
    }

    /**
     * @param mixed $value
     * @return \DateTimeInterface|null
     */
    private static function castDate(mixed $value): ?\DateTimeInterface
    {
        if ($value === null || $value === "") {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value;
        }

        return Carbon::parse($value);
    }
}
