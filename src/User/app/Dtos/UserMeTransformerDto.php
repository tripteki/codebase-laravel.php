<?php

namespace Modules\User\App\Dtos;

use App\Models\User;
use Carbon\Carbon;
use Spatie\LaravelData\Data;

class UserMeTransformerDto extends Data
{
    /**
     * @param string $id
     * @param string $name
     * @param string $email
     * @param \DateTimeInterface|null $email_verified_at
     * @param \DateTimeInterface|null $created_at
     * @param \DateTimeInterface|null $updated_at
     * @param string|null $tenant_id
     * @param \Modules\User\App\Dtos\UserProfileTransformerDto|null $profile
     * @return void
     */
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public ?\DateTimeInterface $email_verified_at,
        public ?\DateTimeInterface $created_at,
        public ?\DateTimeInterface $updated_at,
        public ?string $tenant_id = null,
        public ?UserProfileTransformerDto $profile = null,
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
            email: $user->email,
            email_verified_at: self::castDate($user->email_verified_at),
            created_at: self::castDate($user->created_at),
            updated_at: self::castDate($user->updated_at),
            tenant_id: $user->tenant_id !== null ? (string) $user->tenant_id : null,
            profile: UserProfileTransformerDto::fromProfile($user->profile),
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
