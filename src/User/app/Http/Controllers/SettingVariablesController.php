<?php

namespace Modules\User\App\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\JsonResponse;
use Modules\User\App\Services\SettingService;
use OpenApi\Attributes as OA;

class SettingVariablesController extends BaseController
{
    /**
     * @param SettingService $settingService
     * @return void
     */
    public function __construct(
        protected SettingService $settingService,
    ) {}

    #[OA\Get(
        path: "/api/v1/settings/variables",
        tags: ["Settings"],
        summary: "Settings variables",
        responses: [
            new OA\Response(response: 200, description: "Success."),
        ],
    )]
    public function index(): JsonResponse
    {
        return response()->json([
            "data" => $this->settingService->variables(),
        ], 200);
    }
}
