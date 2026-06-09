<?php

namespace Modules\User\App\Dtos;

use App\Models\Profile;
use Spatie\LaravelData\Data;

class UserProfileTransformerDto extends Data
{
    /**
     * @param string|null $full_name
     * @param string|null $avatar
     * @param string|null $avatar_url
     * @param array<int, string>|null $interests
     * @return void
     */
    public function __construct(
        public ?string $full_name = null,
        public ?string $avatar = null,
        public ?string $avatar_url = null,
        public ?array $interests = null,
    ) {}

    /**
     * @param \App\Models\Profile|null $profile
     * @return self|null
     */
    public static function fromProfile(?Profile $profile): ?self
    {
        if ($profile === null) {
            return null;
        }

        $avatar = $profile->avatar;
        $avatarUrl = null;

        if (filled($avatar)) {
            $avatarUrl = asset("storage/".ltrim((string) $avatar, "/"));
        }

        return new self(
            full_name: $profile->full_name,
            avatar: $avatar,
            avatar_url: $avatarUrl,
            interests: $profile->interests ?? [],
        );
    }
}
