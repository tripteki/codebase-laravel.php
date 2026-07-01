<?php

namespace Modules\User\App\Repositories;

use App\Models\Profile;
use App\Models\User;
use App\Repositories\Repository as BaseRepository;

class UserRepository extends BaseRepository
{
    /**
     * @return \App\Models\User|null
     */
    public function get(): ?User
    {
        $model = $this->getUser();

        return parent::accessGet(
            fn () => $model?->loadMissing("profile") ?? null,
        );
    }

    /**
     * @param array $userData
     * @param array<string, mixed> $profileData
     * @return \App\Models\User|null
     */
    public function create(array $userData, array $profileData = []): ?User
    {
        return parent::mutateCreate(
            function () use ($userData, $profileData): User {
                $user = filled($userData["tenant_id"] ?? null)
                    ? User::query()->withoutTenancy()->create($userData)
                    : User::create($userData);

                if (filled($profileData["full_name"] ?? null)) {
                    Profile::query()->create(array_merge($profileData, [
                        "user_id" => $user->getKey(),
                    ]));
                }

                return $user->fresh([ "profile", ]);
            },
        );
    }

    /**
     * @param \App\Models\User $user
     * @param array $userData
     * @param array $profileData
     * @param string|null $avatarPath
     * @return \App\Models\User
     */
    public function updateMe(User $user, array $userData, array $profileData, ?string $avatarPath = null): User
    {
        return parent::mutateUpdate(
            function () use ($user, $userData, $profileData, $avatarPath): User {
                $user->fill($userData);
                $user->save();

                $profile = $user->profile ?? new Profile([ "user_id" => $user->getKey(), ]);
                $profile->fill($profileData);

                if ($avatarPath !== null) {
                    $profile->avatar = $avatarPath;
                }

                $profile->save();

                return $user->fresh([ "profile", ]);
            },
        );
    }

    /**
     * @return array<int, string>
     */
    public function profileInterests(): array
    {
        return Profile::query()
            ->whereNotNull("interests")
            ->get()
            ->pluck("interests")
            ->flatten()
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->map(static fn ($value) => (string) $value)
            ->all();
    }
}
