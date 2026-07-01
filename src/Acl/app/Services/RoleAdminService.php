<?php

namespace Modules\Acl\App\Services;

use App\Dtos\OffsetPaginationDto;
use App\Services\Service as BaseService;
use App\Support\AdminTenancySupport;
use Illuminate\Support\Facades\Auth;
use Modules\Acl\App\Dtos\RoleDto;
use Modules\Acl\App\Dtos\RoleIdentifierDto;
use Modules\Acl\App\Dtos\RoleTransformerDto;
use Modules\Acl\App\Dtos\RoleUpdateDto;
use Modules\Acl\App\Jobs\RoleAdminExportJob;
use Modules\Acl\App\Jobs\RoleAdminImportJob;
use Modules\Acl\App\Repositories\RoleAdminRepository;

class RoleAdminService extends BaseService
{
    /**
     * @param RoleAdminRepository $roleAdminRepository
     * @return void
     */
    public function __construct(
        protected RoleAdminRepository $roleAdminRepository,
    ) {}

    /**
     * @return OffsetPaginationDto
     */
    public function all(): OffsetPaginationDto
    {
        $paginator = $this->roleAdminRepository->all();

        return $this->toOffsetPagination(
            $paginator,
            fn ($role) => RoleTransformerDto::fromRole($role),
        );
    }

    /**
     * @param RoleIdentifierDto $identifier
     * @return RoleTransformerDto
     */
    public function get(RoleIdentifierDto $identifier): RoleTransformerDto
    {
        return RoleTransformerDto::fromRole($this->roleAdminRepository->get($identifier->id));
    }

    /**
     * @param RoleDto $roleData
     * @return RoleTransformerDto
     */
    public function create(RoleDto $roleData): RoleTransformerDto
    {
        $tenantId = is_central()
            ? AdminTenancySupport::resolveTenantIdFromPayload([
                "tenant" => $roleData->tenant,
            ])
            : current_tenant_id();

        return RoleTransformerDto::fromRole(
            AdminTenancySupport::runWithPermissionsTeam($tenantId, fn () => $this->roleAdminRepository->create(
                [
                    "name" => $roleData->name,
                    "guard_name" => $roleData->guard_name,
                ],
                $roleData->permission_ids,
            )),
        );
    }

    /**
     * @param RoleUpdateDto $roleData
     * @return RoleTransformerDto
     */
    public function update(RoleUpdateDto $roleData): RoleTransformerDto
    {
        return RoleTransformerDto::fromRole(
            $this->roleAdminRepository->update(
                (string) $roleData->id,
                $roleData->updatePayload(),
                $roleData->permission_ids,
            ),
        );
    }

    /**
     * @param RoleIdentifierDto $identifier
     * @return RoleTransformerDto
     */
    public function delete(RoleIdentifierDto $identifier): RoleTransformerDto
    {
        return RoleTransformerDto::fromRole($this->roleAdminRepository->delete($identifier->id));
    }

    /**
     * @param string $path
     * @param string $filename
     * @return string
     */
    public function import(string $path, string $filename): string
    {
        RoleAdminImportJob::dispatch(
            (string) Auth::id(),
            $path,
            $filename,
        );

        return __("acl.role.import.started");
    }

    /**
     * @param string $type
     * @param array<string, mixed> $filters
     * @return string
     */
    public function export(string $type = "csv", array $filters = []): string
    {
        RoleAdminExportJob::dispatch(
            (string) Auth::id(),
            $type,
            $filters,
        );

        return __("acl.role.export.started");
    }
}
