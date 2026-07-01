<?php

namespace Modules\User\App\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\JsonResponse;
use Modules\User\App\Services\SettingService;
use OpenApi\Attributes as OA;

class SettingVariablesController extends BaseController
{
    /**
     * @param \Modules\User\App\Services\SettingService $settingService
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
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json($this->settingService->variables(), 200);
    }
}
