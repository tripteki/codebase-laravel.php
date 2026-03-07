<?php

namespace Modules\User\App\Services;

use Modules\User\App\Dtos\UserDto;
use Modules\User\App\Dtos\UserTransformerDto;
use Modules\User\App\Repositories\UserRepository;
use App\Services\Service as BaseService;

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
     * @param \Modules\User\App\Dtos\UserDto $userData
     * @return \Modules\User\App\Dtos\UserTransformerDto
     */
    public function create(UserDto $userData): UserTransformerDto
    {
        return UserTransformerDto::fromUser($this->userRepository->create($userData->toArray()));
    }
}
