<?php

namespace Modules\Acl\App\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;
use App\Support\AdminTenancySupport;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Acl\App\Dtos\PermissionDto;
use Modules\Acl\App\Dtos\PermissionIdentifierDto;
use Modules\Acl\App\Dtos\PermissionUpdateDto;
use Modules\Acl\App\Models\Permission;
use Modules\Acl\App\Services\PermissionAdminService;
use OpenApi\Attributes as OA;

class PermissionAdminController extends BaseController
{
    use AuthorizesRequests;

    /**
     * @param PermissionAdminService $permissionAdminService
     * @return void
     */
    public function __construct(
        protected PermissionAdminService $permissionAdminService,
    ) {}

    #[OA\Get(
        path: "/api/v1/admin/permissions",
        tags: ["Admin Permissions"],
        summary: "Index",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "limitPage", in: "query", required: false),
            new OA\Parameter(name: "currentPage", in: "query", required: false),
            new OA\Parameter(name: "orders", in: "query", required: false),
            new OA\Parameter(name: "filters", in: "query", required: false),
        ],
        responses: [
            new OA\Response(ref: "#/components/responses/OffsetPaginationSuccess", response: 200),
            new OA\Response(ref: "#/components/responses/Forbidden", response: 403),
        ],
    )]
    public function index(): JsonResponse
    {
        $this->authorize("viewAny", Permission::class);

        return response()->json($this->permissionAdminService->all(), 200);
    }

    #[OA\Get(
        path: "/api/v1/admin/permissions/{id}",
        tags: ["Admin Permissions"],
        summary: "Show",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 403, description: "Forbidden."),
            new OA\Response(response: 404, description: "Not Found."),
        ],
    )]
    public function show(PermissionIdentifierDto $identifier): JsonResponse
    {
        $permission = Permission::findOrFail($identifier->id);
        $this->authorize("view", $permission);

        return response()->json($this->permissionAdminService->get($identifier), 200);
    }

    #[OA\Post(
        path: "/api/v1/admin/permissions",
        tags: ["Admin Permissions"],
        summary: "Store",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 201, description: "Created."),
            new OA\Response(response: 403, description: "Forbidden."),
            new OA\Response(response: 422, description: "Validation Error."),
        ],
    )]
    public function store(PermissionDto $permissionData): JsonResponse
    {
        $this->authorize("create", Permission::class);

        return response()->json($this->permissionAdminService->create($permissionData), 201);
    }

    #[OA\Put(
        path: "/api/v1/admin/permissions/{id}",
        tags: ["Admin Permissions"],
        summary: "Update",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 403, description: "Forbidden."),
            new OA\Response(response: 404, description: "Not Found."),
        ],
    )]
    public function update(PermissionUpdateDto $permissionData): JsonResponse
    {
        $permission = Permission::findOrFail($permissionData->id);
        $this->authorize("update", $permission);

        return response()->json($this->permissionAdminService->update($permissionData), 200);
    }

    #[OA\Delete(
        path: "/api/v1/admin/permissions/{id}",
        tags: ["Admin Permissions"],
        summary: "Delete",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 403, description: "Forbidden."),
            new OA\Response(response: 404, description: "Not Found."),
        ],
    )]
    public function destroy(PermissionIdentifierDto $identifier): JsonResponse
    {
        $permission = Permission::findOrFail($identifier->id);
        $this->authorize("delete", $permission);

        return response()->json($this->permissionAdminService->delete($identifier), 200);
    }

    #[OA\Post(
        path: "/api/v1/admin/permissions/import",
        tags: ["Admin Permissions"],
        summary: "Import",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 403, description: "Forbidden."),
            new OA\Response(response: 422, description: "Validation Error."),
        ],
    )]
    public function import(Request $request): JsonResponse
    {
        $this->authorize("import", Permission::class);

        $request->validate([
            "file" => ["required", "file", "mimes:csv,txt,xls,xlsx"],
        ]);

        $file = $request->file("file");
        $path = $file->store("imports");

        return response()->json(
            $this->permissionAdminService->import(
                Storage::path($path),
                $file->getClientOriginalName(),
            ),
            200,
        );
    }

    #[OA\Post(
        path: "/api/v1/admin/permissions/export",
        tags: ["Admin Permissions"],
        summary: "Export",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "type", in: "query", required: false, description: "Export type (csv, xlsx)."),
        ],
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 403, description: "Forbidden."),
        ],
    )]
    public function export(Request $request): JsonResponse
    {
        $this->authorize("export", Permission::class);

        $type = (string) $request->query("type", "csv");

        return response()->json(
            $this->permissionAdminService->export(
                $type,
                AdminTenancySupport::fromRequest($request),
            ),
            200,
        );
    }
}
