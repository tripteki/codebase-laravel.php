<?php

namespace Modules\User\App\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\User\App\Dtos\SettingBatchUpdateDto;
use Modules\User\App\Dtos\SettingRowUpdateDto;
use Modules\User\App\Services\SettingService;
use OpenApi\Attributes as OA;
use Spatie\LaravelData\DataCollection;

class SettingAdminController extends BaseController
{
    /**
     * @param SettingService $settingService
     * @return void
     */
    public function __construct(
        protected SettingService $settingService,
    ) {}

    #[OA\Get(
        path: "/api/v1/admin/settings",
        tags: ["Admin Settings"],
        summary: "Index",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 403, description: "Forbidden."),
        ],
    )]
    public function index(): JsonResponse
    {
        return response()->json([
            "data" => $this->settingService->all(),
        ], 200);
    }

    #[OA\Put(
        path: "/api/v1/admin/settings",
        tags: ["Admin Settings"],
        summary: "Sync",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 403, description: "Forbidden."),
            new OA\Response(response: 422, description: "Validation Error."),
        ],
    )]
    public function update(Request $request): JsonResponse
    {
        $rawRows = $request->input("rows", []);
        $rows = [];

        if (! is_array($rawRows)) {
            $rawRows = [];
        }

        foreach ($rawRows as $index => $row) {
            if (! is_array($row)) {
                continue;
            }

            $rows[] = SettingRowUpdateDto::from([
                "id" => $row["id"] ?? null,
                "key" => $row["key"] ?? null,
                "value" => $row["value"] ?? null,
                "value_kind" => $row["value_kind"] ?? "text",
                "file" => $request->file("rows.$index.file"),
            ]);
        }

        $payload = SettingBatchUpdateDto::from([
            "rows" => SettingRowUpdateDto::collect($rows, DataCollection::class),
        ]);

        return response()->json([
            "data" => $this->settingService->sync($payload),
        ], 200);
    }
}
