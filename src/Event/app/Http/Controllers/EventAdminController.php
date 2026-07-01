<?php

namespace Modules\Event\App\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;
use App\Support\AdminTenancySupport;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Event\App\Dtos\EventIdentifierDto;
use Modules\Event\App\Models\Event;
use Modules\Event\App\Services\EventAdminService;
use Modules\Event\App\Support\EventFormSupport;
use OpenApi\Attributes as OA;

class EventAdminController extends BaseController
{
    use AuthorizesRequests;

    /**
     * @param EventAdminService $eventAdminService
     * @return void
     */
    public function __construct(
        protected EventAdminService $eventAdminService,
    ) {}

    #[OA\Get(
        path: "/api/v1/admin/events",
        tags: ["Admin Events"],
        summary: "Index",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "limit", in: "query", required: false, description: "Pagination limit."),
            new OA\Parameter(name: "current_page", in: "query", required: false, description: "Pagination current page."),
            new OA\Parameter(name: "sort", in: "query", required: false, description: "Sort field (prefix with - for descending)."),
            new OA\Parameter(name: "filter[q]", in: "query", required: false, description: "Search by slug."),
        ],
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 403, description: "Forbidden."),
        ],
    )]
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $this->authorize("viewAny", Event::class);

        return response()->json($this->eventAdminService->all(), 200);
    }

    #[OA\Get(
        path: "/api/v1/admin/events/{id}",
        tags: ["Admin Events"],
        summary: "Show",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "Event slug"),
        ],
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 403, description: "Forbidden."),
            new OA\Response(response: 404, description: "Not Found."),
        ],
    )]
    /**
     * @param EventIdentifierDto $identifier
     * @return JsonResponse
     */
    public function show(EventIdentifierDto $identifier): JsonResponse
    {
        $event = Event::query()->findOrFail($identifier->id);
        $this->authorize("view", $event);

        return response()->json($this->eventAdminService->get($identifier), 200);
    }

    #[OA\Post(
        path: "/api/v1/admin/events",
        tags: ["Admin Events"],
        summary: "Store",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    required: ["id", "title"],
                    properties: [
                        new OA\Property(property: "id", type: "string", description: "Event slug"),
                        new OA\Property(property: "title", type: "string", description: "Event title"),
                        new OA\Property(property: "description", type: "string", description: "Event description"),
                        new OA\Property(property: "primary_color", type: "string", description: "Primary color"),
                        new OA\Property(property: "secondary_color", type: "string", description: "Secondary color"),
                        new OA\Property(property: "tertiary_color", type: "string", description: "Tertiary color"),
                        new OA\Property(property: "add_ons_features", type: "array", items: new OA\Items(type: "string"), description: "Enabled feature add-ons"),
                        new OA\Property(property: "add_ons_modules", type: "array", items: new OA\Items(type: "string"), description: "Enabled module add-ons"),
                        new OA\Property(property: "add_ons_config", type: "object", description: "Feature configuration keyed by add-on value"),
                        new OA\Property(property: "icon", type: "string", format: "binary", description: "Square logo image"),
                        new OA\Property(property: "favicon_ico", type: "string", format: "binary", description: "Favicon ICO file"),
                        new OA\Property(property: "favicon_png", type: "string", format: "binary", description: "Favicon PNG image"),
                        new OA\Property(property: "brand_light", type: "string", format: "binary", description: "Brand logo for light theme"),
                        new OA\Property(property: "brand_dark", type: "string", format: "binary", description: "Brand logo for dark theme"),
                    ],
                ),
            ),
        ),
        responses: [
            new OA\Response(response: 201, description: "Created."),
            new OA\Response(response: 403, description: "Forbidden."),
            new OA\Response(response: 422, description: "Validation Error."),
        ],
    )]
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize("create", Event::class);

        return response()->json(
            $this->eventAdminService->create(EventFormSupport::validateStore($request)),
            201,
        );
    }

    #[OA\Put(
        path: "/api/v1/admin/events/{id}",
        tags: ["Admin Events"],
        summary: "Update",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "Event slug"),
        ],
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: "title", type: "string", description: "Event title"),
                        new OA\Property(property: "description", type: "string", description: "Event description"),
                        new OA\Property(property: "primary_color", type: "string", description: "Primary color"),
                        new OA\Property(property: "secondary_color", type: "string", description: "Secondary color"),
                        new OA\Property(property: "tertiary_color", type: "string", description: "Tertiary color"),
                        new OA\Property(property: "add_ons_features", type: "array", items: new OA\Items(type: "string"), description: "Enabled feature add-ons"),
                        new OA\Property(property: "add_ons_modules", type: "array", items: new OA\Items(type: "string"), description: "Enabled module add-ons"),
                        new OA\Property(property: "add_ons_config", type: "object", description: "Feature configuration keyed by add-on value"),
                        new OA\Property(property: "icon", type: "string", format: "binary", description: "Square logo image"),
                        new OA\Property(property: "favicon_ico", type: "string", format: "binary", description: "Favicon ICO file"),
                        new OA\Property(property: "favicon_png", type: "string", format: "binary", description: "Favicon PNG image"),
                        new OA\Property(property: "brand_light", type: "string", format: "binary", description: "Brand logo for light theme"),
                        new OA\Property(property: "brand_dark", type: "string", format: "binary", description: "Brand logo for dark theme"),
                    ],
                ),
            ),
        ),
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 403, description: "Forbidden."),
            new OA\Response(response: 404, description: "Not Found."),
            new OA\Response(response: 422, description: "Validation Error."),
        ],
    )]
    /**
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $event = Event::query()->findOrFail($id);
        $this->authorize("update", $event);

        return response()->json(
            $this->eventAdminService->update($id, EventFormSupport::validateUpdate($request, $id)),
            200,
        );
    }

    #[OA\Delete(
        path: "/api/v1/admin/events/{id}",
        tags: ["Admin Events"],
        summary: "Delete",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "Event slug"),
        ],
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 403, description: "Forbidden."),
            new OA\Response(response: 404, description: "Not Found."),
        ],
    )]
    /**
     * @param EventIdentifierDto $identifier
     * @return JsonResponse
     */
    public function destroy(EventIdentifierDto $identifier): JsonResponse
    {
        $event = Event::query()->findOrFail($identifier->id);
        $this->authorize("delete", $event);

        return response()->json($this->eventAdminService->delete($identifier), 200);
    }

    #[OA\Post(
        path: "/api/v1/admin/events/import",
        tags: ["Admin Events"],
        summary: "Import",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    required: ["file"],
                    properties: [
                        new OA\Property(property: "file", type: "string", format: "binary", description: "CSV, TXT, XLS, or XLSX file"),
                    ],
                ),
            ),
        ),
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 403, description: "Forbidden."),
            new OA\Response(response: 422, description: "Validation Error."),
        ],
    )]
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function import(Request $request): JsonResponse
    {
        $this->authorize("import", Event::class);

        $request->validate([
            "file" => ["required", "file", "mimes:csv,txt,xls,xlsx"],
        ]);

        $file = $request->file("file");
        $path = $file->store("imports");

        return response()->json(
            $this->eventAdminService->import(
                Storage::path($path),
                $file->getClientOriginalName(),
            ),
            200,
        );
    }

    #[OA\Post(
        path: "/api/v1/admin/events/export",
        tags: ["Admin Events"],
        summary: "Export",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "type", in: "query", required: false, description: "Export type (csv, xlsx)."),
        ],
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 403, description: "Forbidden."),
        ],
    )]
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function export(Request $request): JsonResponse
    {
        $this->authorize("export", Event::class);

        $type = (string) $request->query("type", "csv");

        return response()->json(
            $this->eventAdminService->export(
                $type,
                AdminTenancySupport::fromRequest($request),
            ),
            200,
        );
    }

    #[OA\Get(
        path: "/api/v1/admin/events/stats/overview",
        tags: ["Admin Events"],
        summary: "Events overview",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 403, description: "Forbidden."),
        ],
    )]
    /**
     * @return JsonResponse
     */
    public function overview(): JsonResponse
    {
        $this->authorize("viewAny", Event::class);

        return response()->json($this->eventAdminService->overview(), 200);
    }
}
