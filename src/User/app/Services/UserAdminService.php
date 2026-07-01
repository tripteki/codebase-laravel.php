<?php

namespace Modules\User\App\Services;

use App\Dtos\OffsetPaginationDto;
use App\Services\Service as BaseService;
use App\Support\AdminTenancySupport;
use Illuminate\Support\Facades\Auth;
use Modules\User\App\Dtos\UserChartStatsDto;
use Modules\User\App\Dtos\UserDto;
use Modules\User\App\Dtos\UserIdentifierDto;
use Modules\User\App\Dtos\UserTransformerDto;
use Modules\User\App\Dtos\UserUpdateDto;
use Modules\User\App\Events\UserAdminActivated;
use Modules\User\App\Events\UserAdminDeactivated;
use Modules\User\App\Jobs\UserAdminExportJob;
use Modules\User\App\Jobs\UserAdminImportJob;
use Modules\User\App\Repositories\UserAdminRepository;

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
     * @return \App\Dtos\OffsetPaginationDto
     */
    public function all(): OffsetPaginationDto
    {
        $paginator = $this->userAdminRepository->all();

        return $this->toOffsetPagination(
            $paginator,
            fn ($user) => UserTransformerDto::fromUser($user),
        );
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
        $tenantId = is_central()
            ? AdminTenancySupport::resolveTenantIdFromPayload([
                "tenant" => $userData->tenant,
            ])
            : current_tenant_id();

        return UserTransformerDto::fromUser($this->userAdminRepository->create(
            $userData->createPayload(),
            $userData->profilePayload(),
            $tenantId,
        ));
    }

    /**
     * @param \Modules\User\App\Dtos\UserUpdateDto $userData
     * @return \Modules\User\App\Dtos\UserTransformerDto
     */
    public function update(UserUpdateDto $userData): UserTransformerDto
    {
        return UserTransformerDto::fromUser(
            $this->userAdminRepository->update(
                (string) $userData->id,
                $userData->updatePayload(),
                $userData->profilePayload(),
            ),
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
    public function forceDelete(UserIdentifierDto $identifier): UserTransformerDto
    {
        $user = $this->userAdminRepository->forceDelete($identifier->id);

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
     * @param array<string, mixed> $filters
     * @return string
     */
    public function export(string $type = "csv", array $filters = []): string
    {
        UserAdminExportJob::dispatch(
            (string) Auth::id(),
            $type,
            $filters,
        );

        return __("User export started.");
    }

    /**
     * @return \Modules\User\App\Dtos\UserChartStatsDto
     */
    public function registrationTrend(): UserChartStatsDto
    {
        $stats = $this->userAdminRepository->registrationTrend();

        return new UserChartStatsDto(
            labels: $stats["labels"],
            series: $stats["series"],
        );
    }

    /**
     * @return \Modules\User\App\Dtos\UserChartStatsDto
     */
    public function usersByRole(): UserChartStatsDto
    {
        $stats = $this->userAdminRepository->usersByRole();

        return new UserChartStatsDto(
            labels: $stats["labels"],
            series: $stats["series"],
        );
    }
}
