<?php

namespace Modules\User\App\Services;

use App\Services\Service as BaseService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\User\App\Dtos\UserAccessTransformerDto;
use Modules\User\App\Dtos\UserDto;
use Modules\User\App\Dtos\UserMeTransformerDto;
use Modules\User\App\Dtos\UserMeUpdateDto;
use Modules\User\App\Dtos\UserTransformerDto;
use Modules\User\App\Repositories\UserRepository;

class UserService extends BaseService
{
    /**
     * @var \Modules\User\App\Repositories\UserRepository
     */
    protected $userRepository;

    /**
     * @param \Modules\User\App\Repositories\UserRepository $userRepository
     * @return void
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @return \Modules\User\App\Dtos\UserTransformerDto
     */
    public function get(): UserTransformerDto
    {
        return UserTransformerDto::fromUser($this->userRepository->get());
    }

    /**
     * @return \Modules\User\App\Dtos\UserMeTransformerDto
     */
    public function getMe(): UserMeTransformerDto
    {
        return UserMeTransformerDto::fromUser($this->userRepository->get());
    }

    /**
     * @param \Modules\User\App\Dtos\UserMeUpdateDto $userData
     * @param \Illuminate\Http\UploadedFile|null $avatar
     * @return \Modules\User\App\Dtos\UserMeTransformerDto
     */
    public function updateMe(UserMeUpdateDto $userData, ?UploadedFile $avatar = null): UserMeTransformerDto
    {
        $user = $this->userRepository->get();
        $avatarPath = null;

        if ($avatar instanceof UploadedFile) {
            if ($user->profile?->avatar) {
                Storage::disk("public")->delete($user->profile->avatar);
            }

            $avatarPath = $avatar->store("avatars", "public");
        }

        $updated = $this->userRepository->updateMe(
            $user,
            $userData->userPayload(),
            $userData->profilePayload(),
            $avatarPath,
        );

        return UserMeTransformerDto::fromUser($updated);
    }

    /**
     * @return array<int, string>
     */
    public function profileInterests(): array
    {
        return $this->userRepository->profileInterests();
    }

    /**
     * @return \Modules\User\App\Dtos\UserAccessTransformerDto
     */
    public function access(): UserAccessTransformerDto
    {
        return UserAccessTransformerDto::fromUser($this->userRepository->get());
    }

    /**
     * @param \Modules\User\App\Dtos\UserDto $userData
     * @return \Modules\User\App\Dtos\UserTransformerDto
     */
    public function create(UserDto $userData): UserTransformerDto
    {
        return UserTransformerDto::fromUser($this->userRepository->create($userData->toArray()));
    }
}
