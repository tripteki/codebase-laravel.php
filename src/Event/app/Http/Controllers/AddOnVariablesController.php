<?php

namespace Modules\Event\App\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\JsonResponse;
use Modules\Event\App\Models\Event;
use Modules\Event\App\Support\AddOnsHelper;
use Modules\Event\App\Support\EventBrandingSupport;
use OpenApi\Attributes as OA;

class AddOnVariablesController extends BaseController
{
    #[OA\Get(
        path: "/api/v1/{tenant}/add-ons/variables",
        tags: ["Add-ons"],
        summary: "Tenant add-on variables",
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 404, description: "Not Found."),
        ],
    )]
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $event = tenant();

        if (! $event instanceof Event) {
            abort(404);
        }

        return response()->json([
            "data" => [
                "features" => AddOnsHelper::featureValues($event),
                "modules" => AddOnsHelper::moduleValues($event),
                "branding" => EventBrandingSupport::variables($event),
            ],
        ], 200);
    }
}
