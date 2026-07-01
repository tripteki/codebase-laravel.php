<?php

namespace Modules\Log\App\Dtos;

use App\Models\User;
use Spatie\LaravelData\Data;

class ActivityCauserTransformerDto extends Data
{
    /**
     * @param string $id
     * @param string|null $full_name
     * @param string $email
     * @return void
     */
    public function __construct(
        public string $id,
        public ?string $full_name,
        public string $email,
    ) {}

    /**
     * @param \App\Models\User $user
     * @return self
     */
    public static function fromUser(User $user): self
    {
        $user->loadMissing("profile");
        $fullName = trim((string) ($user->profile?->full_name ?? ""));

        return new self(
            id: (string) $user->getKey(),
            full_name: $fullName !== "" ? $fullName : null,
            email: (string) $user->email,
        );
    }
}
