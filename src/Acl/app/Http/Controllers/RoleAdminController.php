<?php

namespace Modules\Acl\App\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;
use App\Support\AdminTenancySupport;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Acl\App\Dtos\RoleDto;
use Modules\Acl\App\Dtos\RoleIdentifierDto;
use Modules\Acl\App\Dtos\RoleUpdateDto;
use Modules\Acl\App\Models\Role;
use Modules\Acl\App\Services\RoleAdminService;
use OpenApi\Attributes as OA;

class RoleAdminController extends BaseController
{
    use AuthorizesRequests;

    /**
     * @param RoleAdminService $roleAdminService
     * @return void
     */
    public function __construct(
        protected RoleAdminService $roleAdminService,
    ) {}

    #[OA\Get(
        path: "/api/v1/admin/roles",
        tags: ["Admin Roles"],
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
        $this->authorize("viewAny", Role::class);

        return response()->json($this->roleAdminService->all(), 200);
    }

    #[OA\Get(
        path: "/api/v1/admin/roles/{id}",
        tags: ["Admin Roles"],
        summary: "Show",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 403, description: "Forbidden."),
            new OA\Response(response: 404, description: "Not Found."),
        ],
    )]
    public function show(RoleIdentifierDto $identifier): JsonResponse
    {
        $role = Role::findOrFail($identifier->id);
        $this->authorize("view", $role);

        return response()->json($this->roleAdminService->get($identifier), 200);
    }

    #[OA\Post(
        path: "/api/v1/admin/roles",
        tags: ["Admin Roles"],
        summary: "Store",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 201, description: "Created."),
            new OA\Response(response: 403, description: "Forbidden."),
            new OA\Response(response: 422, description: "Validation Error."),
        ],
    )]
    public function store(RoleDto $roleData): JsonResponse
    {
        $this->authorize("create", Role::class);

        return response()->json($this->roleAdminService->create($roleData), 201);
    }

    #[OA\Put(
        path: "/api/v1/admin/roles/{id}",
        tags: ["Admin Roles"],
        summary: "Update",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 403, description: "Forbidden."),
            new OA\Response(response: 404, description: "Not Found."),
        ],
    )]
    public function update(RoleUpdateDto $roleData): JsonResponse
    {
        $role = Role::findOrFail($roleData->id);
        $this->authorize("update", $role);

        return response()->json($this->roleAdminService->update($roleData), 200);
    }

    #[OA\Delete(
        path: "/api/v1/admin/roles/{id}",
        tags: ["Admin Roles"],
        summary: "Delete",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 403, description: "Forbidden."),
            new OA\Response(response: 404, description: "Not Found."),
        ],
    )]
    public function destroy(RoleIdentifierDto $identifier): JsonResponse
    {
        $role = Role::findOrFail($identifier->id);
        $this->authorize("delete", $role);

        return response()->json($this->roleAdminService->delete($identifier), 200);
    }

    #[OA\Post(
        path: "/api/v1/admin/roles/import",
        tags: ["Admin Roles"],
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
        $this->authorize("import", Role::class);

        $request->validate([
            "file" => ["required", "file", "mimes:csv,txt,xls,xlsx"],
        ]);

        $file = $request->file("file");
        $path = $file->store("imports");

        return response()->json(
            $this->roleAdminService->import(
                Storage::path($path),
                $file->getClientOriginalName(),
            ),
            200,
        );
    }

    #[OA\Post(
        path: "/api/v1/admin/roles/export",
        tags: ["Admin Roles"],
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
        $this->authorize("export", Role::class);

        $type = (string) $request->query("type", "csv");

        return response()->json(
            $this->roleAdminService->export(
                $type,
                AdminTenancySupport::fromRequest($request),
            ),
            200,
        );
    }
}
