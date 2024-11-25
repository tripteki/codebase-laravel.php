<?php

namespace Src\V1\Api\User\Services;

use App\Models\User;
use Src\V1\Api\User\Dtos\UserDto;
use Src\V1\Api\User\Events\UserActivatedEvent;
use Src\V1\Api\User\Events\UserDeactivatedEvent;
use Src\V1\Api\User\Repositories\UserRepository;
use Src\V1\Api\Common\Services\Service as BaseService;
use Spatie\LaravelData\PaginatedDataCollection;

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
    public function all(): PaginatedDataCollection
    {
        $userDto = UserDto::collect($this->userRepository->all(), PaginatedDataCollection::class)->except("password");

        return $userDto;
    }

    /**
     * @return \Src\V1\Api\User\Dtos\UserDto
     */
    public function get(): UserDto
    {
        $userDto = UserDto::from($this->userRepository->get())->except("password");

        return $userDto;
    }

    /**
     * @param \Src\V1\Api\User\Dtos\UserDto $userData
     * @return \Src\V1\Api\User\Dtos\UserDto
     */
    public function update(UserDto $userData): UserDto
    {
        $userDto = UserDto::from($this->userRepository->update($userData->toArray()))->except("password");

        return $userDto;
    }

    /**
     * @param \Src\V1\Api\User\Dtos\UserDto $userData
     * @return \Src\V1\Api\User\Dtos\UserDto
     */
    public function create(UserDto $userData): UserDto
    {
        $userDto = UserDto::from($this->userRepository->create($userData->toArray()))->except("password");

        return $userDto;
    }

    /**
     * @return \Src\V1\Api\User\Dtos\UserDto
     */
    public function restore(): UserDto
    {
        $userDto = UserDto::from($this->userRepository->restore())->except("password");

        broadcast(new UserActivatedEvent($userDto))->toOthers();

        return $userDto;
    }

    /**
     * @return \Src\V1\Api\User\Dtos\UserDto
     */
    public function delete(): UserDto
    {
        $userDto = UserDto::from($this->userRepository->delete())->except("password");

        broadcast(new UserDeactivatedEvent($userDto))->toOthers();

        return $userDto;
    }
}
