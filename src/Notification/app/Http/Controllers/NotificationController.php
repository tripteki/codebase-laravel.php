<?php

namespace Modules\Notification\App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Modules\Acl\App\Enums\GuardEnum;
use Modules\Notification\App\Dtos\NotificationValidatorDto;
use Modules\Notification\App\Enums\PermissionEnum;
use Modules\Notification\App\Dtos\ReadNotificationValidatorDto;
use Modules\Notification\App\Dtos\UnreadNotificationValidatorDto;
use Modules\Notification\App\Services\NotificationService;
use App\Http\Controllers\Controller as BaseController;
use OpenApi\Attributes as OA;

class NotificationController extends BaseController
{
    /**
     * @var \Modules\Notification\App\Services\NotificationService
     */
    protected $notificationService;

    /**
     * @param \Modules\Notification\App\Services\NotificationService $notificationService
     * @return void
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    #[OA\Get(
        path: "/api/v1/notifications",
        tags: ["Notifications"],
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
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json($this->notificationService->all(), 200);
    }

    #[OA\Get(
        path: "/api/v1/notifications/count",
        tags: ["Notifications"],
        summary: "Count",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Success."),
        ]
    )]
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function count(): JsonResponse
    {
        return response()->json($this->notificationService->count(), 200);
    }

    #[OA\Put(
        path: "/api/v1/notifications/read-all",
        tags: ["Notifications"],
        summary: "Read All",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Success."),
        ]
    )]
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function readall(): JsonResponse
    {
        return response()->json($this->notificationService->readall(), 200);
    }

    #[OA\Delete(
        path: "/api/v1/notifications/delete-all",
        tags: ["Notifications"],
        summary: "Delete All",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Success."),
        ]
    )]
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteAll(): JsonResponse
    {
        $this->ensureCanDeleteNotifications();

        return response()->json($this->notificationService->deleteAll(), 200);
    }

    #[OA\Put(
        path: "/api/v1/notifications/read/{id}",
        tags: ["Notifications"],
        summary: "Read",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "Identifier"),
        ],
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 422, description: "Validation Error."),
            new OA\Response(response: 404, description: "Not Found."),
        ]
    )]
    /**
     * @param \Modules\Notification\App\Dtos\UnreadNotificationValidatorDto $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function read(UnreadNotificationValidatorDto $request): JsonResponse
    {
        return response()->json($this->notificationService->read($request), 200);
    }

    #[OA\Get(
        path: "/api/v1/notifications/unread",
        tags: ["Notifications"],
        summary: "Unread",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Success."),
        ]
    )]
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function unread(): JsonResponse
    {
        return response()->json($this->notificationService->unread(), 200);
    }

    #[OA\Get(
        path: "/api/v1/notifications/{id}",
        tags: ["Notifications"],
        summary: "Show",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "Identifier"),
        ],
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 422, description: "Validation Error."),
            new OA\Response(response: 404, description: "Not Found."),
        ]
    )]
    /**
     * @param \Modules\Notification\App\Dtos\NotificationValidatorDto $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(NotificationValidatorDto $request): JsonResponse
    {
        return response()->json($this->notificationService->get($request), 200);
    }

    #[OA\Delete(
        path: "/api/v1/notifications/{id}",
        tags: ["Notifications"],
        summary: "Destroy",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "Identifier"),
        ],
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 422, description: "Validation Error."),
            new OA\Response(response: 404, description: "Not Found."),
        ]
    )]
    /**
     * @param \Modules\Notification\App\Dtos\NotificationValidatorDto $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(NotificationValidatorDto $request): JsonResponse
    {
        $this->ensureCanDeleteNotifications();

        return response()->json($this->notificationService->delete($request), 200);
    }

    /**
     * @return void
     */
    protected function ensureCanDeleteNotifications(): void
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            abort(401, __("auth.not_authorized"));
        }

        if (is_central_superadmin($user)) {
            return;
        }

        $teamId = $user->tenant_id !== null && $user->tenant_id !== ""
            ? (string) $user->tenant_id
            : "";

        if (function_exists("setPermissionsTeamId")) {
            setPermissionsTeamId($teamId);
        }

        try {
            $allowed = $user->hasPermissionTo(PermissionEnum::NOTIFICATION_DELETE->value, GuardEnum::WEB->value);
        } catch (\Throwable) {
            $allowed = false;
        }

        if (! $allowed) {
            abort(403, __("auth.not_authorized"));
        }
    }
}
