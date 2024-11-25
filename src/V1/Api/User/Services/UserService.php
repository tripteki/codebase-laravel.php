<?php

namespace Src\V1\Api\User\Services;

use App\Models\User;
use Src\V1\Api\User\Dtos\UserDto;
use Src\V1\Api\User\Repositories\UserRepository;
use Src\V1\Api\Common\Services\Service as BaseService;

class UserService extends BaseService
{
    /**
     * @var \Src\V1\Api\User\Repositories\UserRepository
     */
    protected $userRepository;

    /**
     * @param \Src\V1\Api\User\Repositories\UserRepository $userRepository
     * @return void
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @return \Src\V1\Api\User\Dtos\UserDto[]
     */
    public function all(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return UserDto::collect($this->userRepository->all());
    }

    /**
     * @return \Src\V1\Api\User\Dtos\UserDto
     */
    public function get(): UserDto
    {
        return UserDto::from($this->userRepository->get());
    }

    /**
     * @param \Src\V1\Api\User\Dtos\UserDto $userData
     * @return \Src\V1\Api\User\Dtos\UserDto
     */
    public function update(array $userData): UserDto
    {
        return UserDto::from($this->userRepository->update(UserDto::validate($userData)));
    }

    /**
     * @return \Src\V1\Api\User\Dtos\UserDto
     */
    public function restore(): UserDto
    {
        return UserDto::from($this->userRepository->restore());
    }

    /**
     * @return \Src\V1\Api\User\Dtos\UserDto
     */
    public function delete(): UserDto
    {
        return UserDto::from($this->userRepository->delete());
    }
}
