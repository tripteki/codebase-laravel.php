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
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function version(): JsonResponse
    {
        return response()->json([
            "status" => 200,
            "message" => "Ok",
            "data" => [
                "version" => (string) config("app.version"),
            ],
        ], 200);
    }

    #[OA\Get(
        path: "/api/status",
        tags: ["App"],
        summary: "Status",
        responses: [
            new OA\Response(response: 200, description: "Health status."),
        ]
    )]
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function status(): JsonResponse
    {
        $memoryThreshold = app()->isProduction() ? 150 * 1024 * 1024 : 500 * 1024 * 1024;
        $heapUsage = memory_get_usage(true);
        $rssUsage = memory_get_usage(false);

        $info = [
            "memory_allocation" => [
                "status" => $heapUsage < $memoryThreshold ? "up" : "down",
                "heap_used" => $heapUsage,
                "heap_used_mb" => round($heapUsage / (1024 * 1024), 2),
                "threshold" => $memoryThreshold,
                "threshold_mb" => round($memoryThreshold / (1024 * 1024), 2),
            ],
            "memory_total" => [
                "status" => $rssUsage < $memoryThreshold ? "up" : "down",
                "rss_used" => $rssUsage,
                "rss_used_mb" => round($rssUsage / (1024 * 1024), 2),
                "threshold" => $memoryThreshold,
                "threshold_mb" => round($memoryThreshold / (1024 * 1024), 2),
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
            "status" => 200,
            "message" => "Ok",
            "data" => [
                "status" => $healthy ? "up" : "down",
                "memory" => [
                    "memory_allocation" => $info["memory_allocation"],
                    "memory_total" => $info["memory_total"],
                ],
                "database" => $info["database"],
                "cache" => $info["cache"],
            ],
        ], 200);
    }
}
