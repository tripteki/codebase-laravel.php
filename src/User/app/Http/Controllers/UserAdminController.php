<?php

namespace Modules\User\App\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;
use App\Models\User;
use App\Support\AdminTenancySupport;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\User\App\Dtos\UserDto;
use Modules\User\App\Dtos\UserIdentifierDto;
use Modules\User\App\Dtos\UserUpdateDto;
use Modules\User\App\Services\UserAdminService;
use OpenApi\Attributes as OA;

class UserAdminController extends BaseController
{
    use AuthorizesRequests;

    /**
     * @var \Modules\User\App\Services\UserAdminService
     */
    protected UserAdminService $userAdminService;

    /**
     * @param \Modules\User\App\Services\UserAdminService $userAdminService
     * @return void
     */
    public function __construct(UserAdminService $userAdminService)
    {
        $this->userAdminService = $userAdminService;
    }

    #[OA\Get(
        path: "/api/v1/admin/users",
        tags: ["Admin Users"],
        summary: "Index",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "limitPage", in: "query", required: false, description: "Pagination page size (default 10, max 100)."),
            new OA\Parameter(name: "currentPage", in: "query", required: false, description: "Pagination current page (default 1)."),
            new OA\Parameter(name: "orders", in: "query", required: false, description: "Sort orders, e.g. created_at:desc,name:asc."),
            new OA\Parameter(name: "filters", in: "query", required: false, description: "Filters, e.g. name:john,email:test."),
        ],
        responses: [
            new OA\Response(ref: "#/components/responses/OffsetPaginationSuccess", response: 200),
            new OA\Response(ref: "#/components/responses/Unauthorized", response: 401),
            new OA\Response(ref: "#/components/responses/Forbidden", response: 403),
        ],
    )]
    public function index(): JsonResponse
    {
        $this->authorize("viewAny", User::class);

        return response()->json($this->userAdminService->all(), 200);
    }

    #[OA\Get(
        path: "/api/v1/admin/users/{id}",
        tags: ["Admin Users"],
        summary: "Show",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "User identifier"),
        ],
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 403, description: "Forbidden."),
            new OA\Response(response: 404, description: "Not Found."),
        ],
    )]
    public function show(UserIdentifierDto $identifier): JsonResponse
    {
        $user = User::findOrFail($identifier->id);
        $this->authorize("view", $user);

        return response()->json($this->userAdminService->get($identifier), 200);
    }

    #[OA\Post(
        path: "/api/v1/admin/users",
        tags: ["Admin Users"],
        summary: "Store",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: "application/x-www-form-urlencoded",
                schema: new OA\Schema(
                    required: ["name", "email", "password"],
                    properties: [
                        new OA\Property(property: "name", type: "string", description: "Name"),
                        new OA\Property(property: "full_name", type: "string", description: "Full Name"),
                        new OA\Property(property: "email", type: "string", description: "Email"),
                        new OA\Property(property: "password", type: "string", description: "Password"),
                        new OA\Property(property: "password_confirmation", type: "string", description: "Password Confirmation"),
                    ],
                ),
            ),
        ),
        responses: [
            new OA\Response(response: 201, description: "Created."),
            new OA\Response(response: 403, description: "Forbidden."),
            new OA\Response(response: 422, description: "Validation Error."),
        ],
    )]
    public function store(UserDto $userData): JsonResponse
    {
        $this->authorize("create", User::class);

        return response()->json($this->userAdminService->create($userData), 201);
    }

    #[OA\Put(
        path: "/api/v1/admin/users/{id}",
        tags: ["Admin Users"],
        summary: "Update",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "User identifier"),
        ],
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: "application/x-www-form-urlencoded",
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: "name", type: "string", description: "Name"),
                        new OA\Property(property: "full_name", type: "string", description: "Full Name"),
                        new OA\Property(property: "email", type: "string", description: "Email"),
                        new OA\Property(property: "password", type: "string", description: "Password"),
                        new OA\Property(property: "password_confirmation", type: "string", description: "Password Confirmation"),
                    ],
                ),
            ),
        ),
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 403, description: "Forbidden."),
            new OA\Response(response: 404, description: "Not Found."),
            new OA\Response(response: 422, description: "Validation Error."),
        ],
    )]
    public function update(UserUpdateDto $userData): JsonResponse
    {
        $user = User::findOrFail($userData->id);
        $this->authorize("update", $user);

        return response()->json($this->userAdminService->update($userData), 200);
    }

    #[OA\Put(
        path: "/api/v1/admin/users/verify/{id}",
        tags: ["Admin Users"],
        summary: "Verify",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "User identifier"),
        ],
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 403, description: "Forbidden."),
            new OA\Response(response: 404, description: "Not Found."),
        ],
    )]
    public function verify(UserIdentifierDto $identifier): JsonResponse
    {
        $user = User::findOrFail($identifier->id);
        $this->authorize("verify", $user);

        return response()->json($this->userAdminService->verify($identifier), 200);
    }

    #[OA\Delete(
        path: "/api/v1/admin/users/deactivate/{id}",
        tags: ["Admin Users"],
        summary: "Deactivate",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "User identifier"),
        ],
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 403, description: "Forbidden."),
            new OA\Response(response: 404, description: "Not Found."),
        ],
    )]
    public function deactivate(UserIdentifierDto $identifier): JsonResponse
    {
        $user = User::withTrashed()->findOrFail($identifier->id);

        if ($user->trashed()) {
            return ApiErrorResponse::message(__("user.admin.already_inactive"), 422);
        }

        $this->authorize("delete", $user);

        return response()->json($this->userAdminService->delete($identifier), 200);
    }

    #[OA\Delete(
        path: "/api/v1/admin/users/force-delete/{id}",
        tags: ["Admin Users"],
        summary: "Force delete",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "User identifier"),
        ],
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 403, description: "Forbidden."),
            new OA\Response(response: 404, description: "Not Found."),
            new OA\Response(response: 422, description: "Validation Error."),
        ],
    )]
    /**
     * @param \Modules\User\App\Dtos\UserIdentifierDto $identifier
     * @return \Illuminate\Http\JsonResponse
     */
    public function forceDelete(UserIdentifierDto $identifier): JsonResponse
    {
        $user = User::withTrashed()->findOrFail($identifier->id);

        if (! $user->trashed()) {
            return ApiErrorResponse::message(__("user.admin.not_inactive"), 422);
        }

        $this->authorize("delete", $user);

        return response()->json($this->userAdminService->forceDelete($identifier), 200);
    }

    #[OA\Delete(
        path: "/api/v1/admin/users/activate/{id}",
        tags: ["Admin Users"],
        summary: "Activate",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "User identifier"),
        ],
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 403, description: "Forbidden."),
            new OA\Response(response: 404, description: "Not Found."),
        ],
    )]
    public function activate(UserIdentifierDto $identifier): JsonResponse
    {
        $user = User::withTrashed()->findOrFail($identifier->id);
        $this->authorize("restore", $user);

        return response()->json($this->userAdminService->restore($identifier), 200);
    }

    #[OA\Post(
        path: "/api/v1/admin/users/import",
        tags: ["Admin Users"],
        summary: "Import",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    required: ["file"],
                    properties: [
                        new OA\Property(property: "file", type: "string", format: "binary", description: "CSV, TXT, XLS, or XLSX file"),
                    ],
                ),
            ),
        ),
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 403, description: "Forbidden."),
            new OA\Response(response: 422, description: "Validation Error."),
        ],
    )]
    public function import(Request $request): JsonResponse
    {
        $this->authorize("import", User::class);

        $request->validate([
            "file" => [ "required", "file", "mimes:csv,txt,xls,xlsx", ],
        ]);

        $file = $request->file("file");
        $path = $file->store("imports");

        return response()->json(
            $this->userAdminService->import(
                Storage::path($path),
                $file->getClientOriginalName(),
            ),
            200,
        );
    }

    #[OA\Post(
        path: "/api/v1/admin/users/export",
        tags: ["Admin Users"],
        summary: "Export",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "type", in: "query", required: false, description: "Export type (csv, xls, xlsx). Alias: export_type."),
            new OA\Parameter(name: "export_type", in: "query", required: false, description: "Export type (csv, xls, xlsx)."),
        ],
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 403, description: "Forbidden."),
        ],
    )]
    public function export(Request $request): JsonResponse
    {
        $this->authorize("export", User::class);

        $type = (string) ($request->query("export_type") ?? $request->query("type", "csv"));

        return response()->json(
            $this->userAdminService->export(
                $type,
                AdminTenancySupport::fromRequest($request),
            ),
            200,
        );
    }

    #[OA\Get(
        path: "/api/v1/admin/users/stats/registrations",
        tags: ["Admin Users"],
        summary: "Registration trend",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 403, description: "Forbidden."),
        ],
    )]
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function registrationTrend(): JsonResponse
    {
        $this->authorize("viewAny", User::class);

        return response()->json($this->userAdminService->registrationTrend(), 200);
    }

    #[OA\Get(
        path: "/api/v1/admin/users/stats/roles",
        tags: ["Admin Users"],
        summary: "Users by role",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 403, description: "Forbidden."),
        ],
    )]
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function usersByRole(): JsonResponse
    {
        $this->authorize("viewAny", User::class);

        return response()->json($this->userAdminService->usersByRole(), 200);
    }
}
