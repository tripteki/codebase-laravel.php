<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class AppController extends Controller
{
    #[OA\Get(
        path: "/api/version",
        tags: ["App"],
        summary: "Version",
        responses: [
            new OA\Response(response: 200, description: "Success."),
        ]
    )]
    public function version(): JsonResponse
    {
        return response()->json([
            "version" => (string) config("app.version"),
        ], 200);
    }

    #[OA\Get(
        path: "/api/status",
        tags: ["App"],
        summary: "Status",
        responses: [
            new OA\Response(response: 200, description: "Healthy."),
            new OA\Response(response: 503, description: "Unhealthy."),
        ]
    )]
    public function status(): JsonResponse
    {
        $memoryThreshold = app()->isProduction() ? 150 * 1024 * 1024 : 500 * 1024 * 1024;
        $heapUsage = memory_get_usage(true);
        $rssUsage = memory_get_usage(false);

        $info = [
            "memory_allocation" => [
                "status" => $heapUsage < $memoryThreshold ? "up" : "down",
            ],
            "memory_total" => [
                "status" => $rssUsage < $memoryThreshold ? "up" : "down",
            ],
        ];

        try {
            DB::connection()->getPdo();
            $info["database"] = [ "status" => "up", ];
        } catch (\Throwable) {
            $info["database"] = [ "status" => "down", ];
        }

        try {
            Cache::store(config("cache.default"))->put("health_check", true, 1);
            $info["cache"] = [ "status" => "up", ];
        } catch (\Throwable) {
            $info["cache"] = [ "status" => "down", ];
        }

        $healthy = collect($info)->every(fn (array $check) => ($check["status"] ?? "down") === "up");

        return response()->json([
            "status" => $healthy ? "ok" : "error",
            "info" => $info,
            "error" => $healthy ? (object) [] : $info,
            "details" => $info,
        ], $healthy ? 200 : 503);
    }
}
