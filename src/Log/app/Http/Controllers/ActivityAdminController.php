<?php

namespace Modules\Log\App\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Modules\Log\App\Dtos\ActivityIdentifierDto;
use Modules\Log\App\Models\Activity;
use Modules\Log\App\Services\ActivityAdminService;
use OpenApi\Attributes as OA;

class ActivityAdminController extends BaseController
{
    use AuthorizesRequests;

    /**
     * @param ActivityAdminService $activityAdminService
     * @return void
     */
    public function __construct(
        protected ActivityAdminService $activityAdminService,
    ) {}

    #[OA\Get(
        path: "/api/v1/admin/activities",
        tags: ["Admin Activities"],
        summary: "Index",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "limitPage", in: "query", required: false, description: "Pagination page size (default 10, max 100)."),
            new OA\Parameter(name: "currentPage", in: "query", required: false, description: "Pagination current page (default 1)."),
            new OA\Parameter(name: "orders", in: "query", required: false, description: "Sort orders, e.g. created_at:desc."),
            new OA\Parameter(name: "filters", in: "query", required: false, description: "Filters, e.g. q:profile,log_name:default."),
        ],
        responses: [
            new OA\Response(ref: "#/components/responses/OffsetPaginationSuccess", response: 200),
            new OA\Response(ref: "#/components/responses/Unauthorized", response: 401),
            new OA\Response(ref: "#/components/responses/Forbidden", response: 403),
        ],
    )]
    public function index(): JsonResponse
    {
        $this->authorize("viewAny", Activity::class);

        return response()->json($this->activityAdminService->all(), 200);
    }

    #[OA\Get(
        path: "/api/v1/admin/activities/{id}",
        tags: ["Admin Activities"],
        summary: "Show",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "Activity identifier"),
        ],
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 403, description: "Forbidden."),
            new OA\Response(response: 404, description: "Not Found."),
        ],
    )]
    public function show(ActivityIdentifierDto $identifier): JsonResponse
    {
        $activity = Activity::findOrFail($identifier->id);
        $this->authorize("view", $activity);

        return response()->json($this->activityAdminService->get($identifier), 200);
    }
}
