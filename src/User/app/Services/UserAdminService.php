<?php

namespace Modules\User\App\Services;

use Illuminate\Support\Facades\Auth;
use Modules\User\App\Dtos\UserDto;
use Modules\User\App\Dtos\UserIdentifierDto;
use Modules\User\App\Dtos\UserTransformerDto;
use Modules\User\App\Dtos\UserUpdateDto;
use Modules\User\App\Events\UserAdminActivated;
use Modules\User\App\Events\UserAdminDeactivated;
use Modules\User\App\Jobs\UserAdminExportJob;
use Modules\User\App\Jobs\UserAdminImportJob;
use Modules\User\App\Repositories\UserAdminRepository;
use App\Services\Service as BaseService;
use Spatie\LaravelData\PaginatedDataCollection;

class UserAdminService extends BaseService
{
    /**
     * @var \Modules\User\App\Repositories\UserAdminRepository
     */
    protected UserAdminRepository $userAdminRepository;

    /**
     * @param \Modules\User\App\Repositories\UserAdminRepository $userAdminRepository
     * @return void
     */
    public function __construct(UserAdminRepository $userAdminRepository)
    {
        $this->userAdminRepository = $userAdminRepository;
    }

    /**
     * @return \Spatie\LaravelData\PaginatedDataCollection
     */
    public function all(): PaginatedDataCollection
    {
        $paginator = $this->userAdminRepository->all();
        $paginator->setCollection(
            $paginator->getCollection()->map(fn ($user) => UserTransformerDto::fromUser($user))
        );

        return UserTransformerDto::collect($paginator, PaginatedDataCollection::class);
    }

    /**
     * @param \Modules\User\App\Dtos\UserIdentifierDto $identifier
     * @return \Modules\User\App\Dtos\UserTransformerDto
     */
    public function get(UserIdentifierDto $identifier): UserTransformerDto
    {
        return UserTransformerDto::fromUser($this->userAdminRepository->get($identifier->id));
    }

    /**
     * @param \Modules\User\App\Dtos\UserDto $userData
     * @return \Modules\User\App\Dtos\UserTransformerDto
     */
    public function create(UserDto $userData): UserTransformerDto
    {
        return UserTransformerDto::fromUser($this->userAdminRepository->create($userData->toArray()));
    }

    /**
     * @param \Modules\User\App\Dtos\UserUpdateDto $userData
     * @return \Modules\User\App\Dtos\UserTransformerDto
     */
    public function update(UserUpdateDto $userData): UserTransformerDto
    {
        return UserTransformerDto::fromUser(
            $this->userAdminRepository->update((string) $userData->id, $userData->updatePayload())
        );
    }

    /**
     * @param \Modules\User\App\Dtos\UserIdentifierDto $identifier
     * @return \Modules\User\App\Dtos\UserTransformerDto
     */
    public function delete(UserIdentifierDto $identifier): UserTransformerDto
    {
        $user = $this->userAdminRepository->delete($identifier->id);
        event(new UserAdminDeactivated($user, (string) Auth::id()));

        return UserTransformerDto::fromUser($user);
    }

    /**
     * @param \Modules\User\App\Dtos\UserIdentifierDto $identifier
     * @return \Modules\User\App\Dtos\UserTransformerDto
     */
    public function restore(UserIdentifierDto $identifier): UserTransformerDto
    {
        $user = $this->userAdminRepository->restore($identifier->id);
        event(new UserAdminActivated($user, (string) Auth::id()));

        return UserTransformerDto::fromUser($user);
    }

    /**
     * @param \Modules\User\App\Dtos\UserIdentifierDto $identifier
     * @return \Modules\User\App\Dtos\UserTransformerDto
     */
    public function verify(UserIdentifierDto $identifier): UserTransformerDto
    {
        return UserTransformerDto::fromUser($this->userAdminRepository->verify($identifier->id));
    }

    /**
     * @param string $path
     * @param string $filename
     * @return string
     */
    public function import(string $path, string $filename): string
    {
        UserAdminImportJob::dispatch(
            (string) Auth::id(),
            $path,
            $filename,
        );

        return __("User import started.");
    }

    /**
     * @param string $type
     * @return string
     */
    public function export(string $type = "csv"): string
    {
        UserAdminExportJob::dispatch(
            (string) Auth::id(),
            $type,
        );

        return __("User export started.");
    }
}
