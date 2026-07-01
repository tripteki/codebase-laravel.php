<?php

namespace Modules\User\App\Dtos;

use App\Models\User;
use Carbon\Carbon;
use Modules\Acl\App\Enums\RoleEnum;
use Spatie\LaravelData\Data;

class UserTransformerDto extends Data
{
    /**
     * @param string $id
     * @param string $name
     * @param string|null $full_name
     * @param string $email
     * @param string|null $role
     * @param string|null $avatar
     * @param string|null $avatar_url
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
        public ?string $role,
        public ?string $avatar,
        public ?string $avatar_url,
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
        $profile = UserProfileTransformerDto::fromProfile($user->profile);

        $dto = new self(
            id: (string) $user->getKey(),
            name: $user->name,
            full_name: $user->profile?->full_name,
            email: $user->email,
            role: self::primaryRole($user),
            avatar: $profile?->avatar,
            avatar_url: $profile?->avatar_url,
            email_verified_at: self::castDate($user->email_verified_at),
            created_at: self::castDate($user->created_at),
            updated_at: self::castDate($user->updated_at),
            deleted_at: self::castDate($user->deleted_at),
            tenant_id: $user->tenant_id !== null ? (string) $user->tenant_id : null,
        );

        if (! is_tenancy()) {
            return $dto->except("tenant_id");
        }

        return $dto;
    }

    /**
     * @param \App\Models\User $user
     * @return string|null
     */
    public static function primaryRole(User $user): ?string
    {
        $names = $user->getRoleNames()->all();
        $priority = [
            RoleEnum::SUPERADMIN->value,
            RoleEnum::ADMIN->value,
            RoleEnum::USER->value,
        ];

        foreach ($priority as $name) {
            if (in_array($name, $names, true)) {
                return $name;
            }
        }

        $first = $names[0] ?? null;

        return is_string($first) && $first !== "" ? $first : null;
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
