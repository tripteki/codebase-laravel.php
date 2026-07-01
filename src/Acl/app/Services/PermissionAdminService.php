<?php

namespace Modules\Acl\App\Services;

use App\Dtos\OffsetPaginationDto;
use App\Services\Service as BaseService;
use App\Support\AdminTenancySupport;
use Illuminate\Support\Facades\Auth;
use Modules\Acl\App\Dtos\PermissionDto;
use Modules\Acl\App\Dtos\PermissionIdentifierDto;
use Modules\Acl\App\Dtos\PermissionTransformerDto;
use Modules\Acl\App\Dtos\PermissionUpdateDto;
use Modules\Acl\App\Jobs\PermissionAdminExportJob;
use Modules\Acl\App\Jobs\PermissionAdminImportJob;
use Modules\Acl\App\Repositories\PermissionAdminRepository;

class PermissionAdminService extends BaseService
{
    /**
     * @param \Modules\Acl\App\Repositories\PermissionAdminRepository $permissionAdminRepository
     * @return void
     */
    public function __construct(
        protected PermissionAdminRepository $permissionAdminRepository,
    ) {}

    /**
     * @return \App\Dtos\OffsetPaginationDto
     */
    public function all(): OffsetPaginationDto
    {
        $paginator = $this->permissionAdminRepository->all();

        return $this->toOffsetPagination(
            $paginator,
            fn ($permission) => PermissionTransformerDto::fromPermission($permission),
        );
    }

    /**
     * @param \Modules\Acl\App\Dtos\PermissionIdentifierDto $identifier
     * @return \Modules\Acl\App\Dtos\PermissionTransformerDto
     */
    public function get(PermissionIdentifierDto $identifier): PermissionTransformerDto
    {
        return PermissionTransformerDto::fromPermission($this->permissionAdminRepository->get($identifier->id));
    }

    /**
     * @param \Modules\Acl\App\Dtos\PermissionDto $permissionData
     * @return \Modules\Acl\App\Dtos\PermissionTransformerDto
     */
    public function create(PermissionDto $permissionData): PermissionTransformerDto
    {
        $tenantId = is_central()
            ? AdminTenancySupport::resolveTenantIdFromPayload([
                "tenant" => $permissionData->tenant,
            ])
            : current_tenant_id();

        $payload = [
            "name" => $permissionData->name,
            "guard_name" => filled($permissionData->guard_name) ? (string) $permissionData->guard_name : "web",
        ];

        if ($tenantId !== null) {
            $payload["tenant_id"] = $tenantId;
        }

        return PermissionTransformerDto::fromPermission(
            AdminTenancySupport::runWithPermissionsTeam($tenantId, fn () => $this->permissionAdminRepository->create($payload)),
        );
    }

    /**
     * @param \Modules\Acl\App\Dtos\PermissionUpdateDto $permissionData
     * @return \Modules\Acl\App\Dtos\PermissionTransformerDto
     */
    public function update(PermissionUpdateDto $permissionData): PermissionTransformerDto
    {
        return PermissionTransformerDto::fromPermission(
            $this->permissionAdminRepository->update(
                (string) $permissionData->id,
                $permissionData->updatePayload(),
            ),
        );
    }

    /**
     * @param \Modules\Acl\App\Dtos\PermissionIdentifierDto $identifier
     * @return \Modules\Acl\App\Dtos\PermissionTransformerDto
     */
    public function delete(PermissionIdentifierDto $identifier): PermissionTransformerDto
    {
        return PermissionTransformerDto::fromPermission($this->permissionAdminRepository->delete($identifier->id));
    }

    /**
     * @param string $path
     * @param string $filename
     * @return string
     */
    public function import(string $path, string $filename): string
    {
        PermissionAdminImportJob::dispatch(
            (string) Auth::id(),
            $path,
            $filename,
        );

        return __("acl.permission.import.started");
    }

    /**
     * @param string $type
     * @param array<string, mixed> $filters
     * @return string
     */
    public function export(string $type = "csv", array $filters = []): string
    {
        PermissionAdminExportJob::dispatch(
            (string) Auth::id(),
            $type,
            $filters,
        );

        return __("acl.permission.export.started");
    }
}
