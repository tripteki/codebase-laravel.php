<?php

namespace Modules\User\App\Dtos;

use App\Models\User;
use Carbon\Carbon;
use Spatie\LaravelData\Data;

class UserTransformerDto extends Data
{
    /**
     * @param string $id
     * @param string $name
     * @param string|null $full_name
     * @param string $email
     * @param \DateTimeInterface|null $email_verified_at
     * @param \DateTimeInterface|null $created_at
     * @param \DateTimeInterface|null $updated_at
     * @param \DateTimeInterface|null $deleted_at
     * @param string|null $tenant_id
     * @return void
     */
    public function __construct(
        public string $id,
        public string $name,
        public ?string $full_name,
        public string $email,
        public ?\DateTimeInterface $email_verified_at,
        public ?\DateTimeInterface $created_at,
        public ?\DateTimeInterface $updated_at,
        public ?\DateTimeInterface $deleted_at = null,
        public ?string $tenant_id = null,
    ) {}

    /**
     * @param \App\Models\User $user
     * @return self
     */
    public static function fromUser(User $user): self
    {
        $user->loadMissing("profile");

        return new self(
            id: (string) $user->getKey(),
            name: $user->name,
            full_name: $user->profile?->full_name,
            email: $user->email,
            email_verified_at: self::castDate($user->email_verified_at),
            created_at: self::castDate($user->created_at),
            updated_at: self::castDate($user->updated_at),
            deleted_at: self::castDate($user->deleted_at),
            tenant_id: $user->tenant_id !== null ? (string) $user->tenant_id : null,
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
