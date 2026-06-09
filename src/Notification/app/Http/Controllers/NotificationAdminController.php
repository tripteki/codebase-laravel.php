<?php

namespace Modules\Notification\App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Modules\Notification\App\Dtos\NotificationValidatorDto;
use Modules\Notification\App\Models\Notification;
use Modules\Notification\App\Services\NotificationAdminService;
use App\Http\Controllers\Controller as BaseController;
use OpenApi\Attributes as OA;

class NotificationAdminController extends BaseController
{
    use AuthorizesRequests;

    /**
     * @var \Modules\Notification\App\Services\NotificationAdminService
     */
    protected NotificationAdminService $notificationAdminService;

    /**
     * @param \Modules\Notification\App\Services\NotificationAdminService $notificationAdminService
     * @return void
     */
    public function __construct(NotificationAdminService $notificationAdminService)
    {
        $this->notificationAdminService = $notificationAdminService;
    }

    #[OA\Get(
        path: "/api/v1/admin/notifications",
        tags: ["Admin Notifications"],
        summary: "Index",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "limitPage", in: "query", required: false, description: "Pagination page size (default 10, max 100)."),
            new OA\Parameter(name: "currentPage", in: "query", required: false, description: "Pagination current page (default 1)."),
            new OA\Parameter(name: "orders", in: "query", required: false, description: "Sort orders, e.g. updated_at:desc."),
            new OA\Parameter(name: "filters", in: "query", required: false, description: "Filters, e.g. status:unread."),
        ],
        responses: [
            new OA\Response(ref: "#/components/responses/OffsetPaginationSuccess", response: 200),
            new OA\Response(ref: "#/components/responses/Unauthorized", response: 401),
            new OA\Response(ref: "#/components/responses/Forbidden", response: 403),
        ]
    )]
    public function index(): JsonResponse
    {
        $this->authorize("viewAny", Notification::class);

        return response()->json($this->notificationAdminService->all(), 200);
    }

    #[OA\Get(
        path: "/api/v1/admin/notifications/{id}",
        tags: ["Admin Notifications"],
        summary: "Show",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "Notification identifier"),
        ],
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 403, description: "Forbidden."),
            new OA\Response(response: 404, description: "Not Found."),
        ]
    )]
    public function show(NotificationValidatorDto $request): JsonResponse
    {
        $notification = Notification::withTrashed()->findOrFail($request->id);
        $this->authorize("view", $notification);

        return response()->json($this->notificationAdminService->get($request), 200);
    }

    #[OA\Delete(
        path: "/api/v1/admin/notifications/deactivate/{id}",
        tags: ["Admin Notifications"],
        summary: "Deactivate",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "Notification identifier"),
        ],
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 403, description: "Forbidden."),
            new OA\Response(response: 404, description: "Not Found."),
        ]
    )]
    public function deactivate(NotificationValidatorDto $request): JsonResponse
    {
        $notification = Notification::findOrFail($request->id);
        $this->authorize("delete", $notification);

        return response()->json($this->notificationAdminService->delete($request), 200);
    }

    #[OA\Delete(
        path: "/api/v1/admin/notifications/activate/{id}",
        tags: ["Admin Notifications"],
        summary: "Activate",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "Notification identifier"),
        ],
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 403, description: "Forbidden."),
            new OA\Response(response: 404, description: "Not Found."),
        ]
    )]
    public function activate(NotificationValidatorDto $request): JsonResponse
    {
        $notification = Notification::withTrashed()->findOrFail($request->id);
        $this->authorize("restore", $notification);

        return response()->json($this->notificationAdminService->restore($request), 200);
    }
}
