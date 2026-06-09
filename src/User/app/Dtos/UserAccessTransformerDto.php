<?php

namespace Modules\User\App\Dtos;

use App\Models\User;
use Spatie\LaravelData\Data;

class UserAccessTransformerDto extends Data
{
    /**
     * @param list<string> $permissions
     * @param list<string> $roles
     * @return void
     */
    public function __construct(
        public array $permissions,
        public array $roles,
    ) {}

    /**
     * @param \App\Models\User $user
     * @return self
     */
    public static function fromUser(User $user): self
    {
        return new self(
            permissions: $user->getAllPermissions()->pluck("name")->values()->all(),
            roles: $user->getRoleNames()->values()->all(),
        );
    }
}
