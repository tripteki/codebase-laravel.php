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
use Modules\Acl\App\Models\Permission;
use Modules\Acl\App\Repositories\RoleAdminRepository;

class RoleAdminService extends BaseService
{
    /**
     * @param \Modules\Acl\App\Repositories\RoleAdminRepository $roleAdminRepository
     * @return void
     */
    public function __construct(
        protected RoleAdminRepository $roleAdminRepository,
    ) {}

    /**
     * @return \App\Dtos\OffsetPaginationDto
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
     * @param \Modules\Acl\App\Dtos\RoleIdentifierDto $identifier
     * @return \Modules\Acl\App\Dtos\RoleTransformerDto
     */
    public function get(RoleIdentifierDto $identifier): RoleTransformerDto
    {
        return RoleTransformerDto::fromRole($this->roleAdminRepository->get($identifier->id));
    }

    /**
     * @param \Modules\Acl\App\Dtos\RoleDto $roleData
     * @return \Modules\Acl\App\Dtos\RoleTransformerDto
     */
    public function create(RoleDto $roleData): RoleTransformerDto
    {
        $tenantId = is_central()
            ? AdminTenancySupport::resolveTenantIdFromPayload([
                "tenant" => $roleData->tenant,
            ])
            : current_tenant_id();

        return RoleTransformerDto::fromRole(
            AdminTenancySupport::runWithPermissionsTeam($tenantId, function () use ($roleData) {
                $this->assertPermissionIds($roleData->permission_ids);

                return $this->roleAdminRepository->create(
                    [
                        "name" => $roleData->name,
                        "guard_name" => filled($roleData->guard_name) ? (string) $roleData->guard_name : "web",
                    ],
                    $roleData->permission_ids,
                );
            }),
        );
    }

    /**
     * @param \Modules\Acl\App\Dtos\RoleUpdateDto $roleData
     * @return \Modules\Acl\App\Dtos\RoleTransformerDto
     */
    public function update(RoleUpdateDto $roleData): RoleTransformerDto
    {
        $this->assertPermissionIds($roleData->permission_ids);

        return RoleTransformerDto::fromRole(
            $this->roleAdminRepository->update(
                (string) $roleData->id,
                $roleData->updatePayload(),
                $roleData->permission_ids,
            ),
        );
    }

    /**
     * @param \Modules\Acl\App\Dtos\RoleIdentifierDto $identifier
     * @return \Modules\Acl\App\Dtos\RoleTransformerDto
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

    /**
     * @param array<int, string>|null $permissionIds
     * @return void
     */
    protected function assertPermissionIds(?array $permissionIds): void
    {
        if ($permissionIds === null || $permissionIds === []) {
            return;
        }

        $ids = array_values(array_unique(array_map(
            static fn (mixed $id): string => (string) $id,
            $permissionIds,
        )));
        $found = Permission::query()
            ->whereIn("id", $ids)
            ->pluck("id")
            ->map(static fn (mixed $id): string => (string) $id)
            ->all();

        if (count($found) !== count($ids)) {
            abort(422, __("acl.role.permissions_not_found"));
        }
    }
}
