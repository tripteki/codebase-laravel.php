<?php

namespace App\Http\Controllers;

use App\Services\AdminDashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class AdminDashboardController extends Controller
{
    /**
     * @param \App\Services\AdminDashboardService $adminDashboardService
     * @return void
     */
    public function __construct(
        protected AdminDashboardService $adminDashboardService,
    ) {}

    #[OA\Get(
        path: "/api/v1/admin/dashboard",
        tags: ["DashboardAdmin"],
        summary: "Admin dashboard",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "period", in: "query", required: false, description: "today|7d|30d|custom"),
            new OA\Parameter(name: "date_from", in: "query", required: false),
            new OA\Parameter(name: "date_to", in: "query", required: false),
        ],
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 401, description: "Unauthorized."),
            new OA\Response(response: 403, description: "Forbidden."),
        ],
    )]
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        return response()->json($this->adminDashboardService->get(
            $request->user(),
            (string) $request->query("period", "30d"),
            $request->query("date_from"),
            $request->query("date_to"),
        ), 200);
    }
}
