<?php

namespace Modules\Notification\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Modules\Notification\App\Dtos\NotificationValidatorDto;
use Modules\Notification\App\Dtos\ReadNotificationValidatorDto;
use Modules\Notification\App\Dtos\UnreadNotificationValidatorDto;
use Modules\Notification\App\Services\NotificationService;
use App\Http\Controllers\Controller as BaseController;

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

    /**
     * @OA\Get(
     *      path="/api/v1/notifications",
     *      tags={"Notifications"},
     *      summary="Index",
     *      security={{ "bearerAuth": {} }},
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="limit",
     *          description="Pagination Limit."
     *      ),
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="current_page",
     *          description="Pagination Current Page."
     *      ),
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="order",
     *          description="Pagination Order."
     *      ),
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="filter[]",
     *          description="Pagination Filter."
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success."
     *      )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json($this->notificationService->all(), 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function count(): JsonResponse
    {
        return response()->json($this->notificationService->count(), 200);
    }

    /**
     * @OA\Put(
     *      path="/api/v1/notifications/read-all",
     *      tags={"Notifications"},
     *      summary="Read All",
     *      security={{ "bearerAuth": {} }},
     *      @OA\Response(
     *          response=200,
     *          description="Success."
     *      )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function readall(): JsonResponse
    {
        return response()->json($this->notificationService->readall(), 200);
    }

    /**
     * @OA\Put(
     *      path="/api/v1/notifications/read/{id}",
     *      tags={"Notifications"},
     *      summary="Read",
     *      security={{ "bearerAuth": {} }},
     *      @OA\Parameter(
     *          required=true,
     *          in="path",
     *          name="id",
     *          description="Identifier"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success."
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation Error."
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not Found."
     *      )
     * )
     *
     * @param \Modules\Notification\App\Dtos\UnreadNotificationValidatorDto $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function read(UnreadNotificationValidatorDto $request): JsonResponse
    {
        return response()->json($this->notificationService->read($request), 200);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/notifications/unread",
     *      tags={"Notifications"},
     *      summary="Unread",
     *      security={{ "bearerAuth": {} }},
     *      @OA\Response(
     *          response=200,
     *          description="Success."
     *      )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function unread(): JsonResponse
    {
        return response()->json($this->notificationService->unread(), 200);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/notifications/{id}",
     *      tags={"Notifications"},
     *      summary="Show",
     *      security={{ "bearerAuth": {} }},
     *      @OA\Parameter(
     *          required=true,
     *          in="path",
     *          name="id",
     *          description="Identifier"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success."
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation Error."
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not Found."
     *      )
     * )
     *
     * @param \Modules\Notification\App\Dtos\NotificationValidatorDto $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(NotificationValidatorDto $request): JsonResponse
    {
        return response()->json($this->notificationService->get($request), 200);
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/notifications/{id}",
     *      tags={"Notifications"},
     *      summary="Destroy",
     *      security={{ "bearerAuth": {} }},
     *      @OA\Parameter(
     *          required=true,
     *          in="path",
     *          name="id",
     *          description="Identifier"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success."
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation Error."
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not Found."
     *      )
     * )
     *
     * @param \Modules\Notification\App\Dtos\NotificationValidatorDto $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(NotificationValidatorDto $request): JsonResponse
    {
        return response()->json($this->notificationService->delete($request), 200);
    }
}
