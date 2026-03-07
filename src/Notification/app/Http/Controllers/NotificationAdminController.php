<?php

namespace Modules\Notification\App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Modules\Notification\App\Dtos\NotificationValidatorDto;
use Modules\Notification\App\Models\Notification;
use Modules\Notification\App\Services\NotificationAdminService;
use App\Http\Controllers\Controller as BaseController;

class NotificationAdminController extends BaseController
{
    use AuthorizesRequests;

    /**
     * @var \Modules\Notification\App\Services\NotificationAdminService
     */
    protected NotificationAdminService $notificationAdminService;

    /**
     * @param \Modules\Notification\App\Services\NotificationAdminService $notificationAdminService
     * @return void
     */
    public function __construct(NotificationAdminService $notificationAdminService)
    {
        $this->notificationAdminService = $notificationAdminService;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $this->authorize("viewAny", Notification::class);

        return response()->json($this->notificationAdminService->all(), 200);
    }

    /**
     * @param \Modules\Notification\App\Dtos\NotificationValidatorDto $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(NotificationValidatorDto $request): JsonResponse
    {
        $notification = Notification::withTrashed()->findOrFail($request->id);
        $this->authorize("view", $notification);

        return response()->json($this->notificationAdminService->get($request), 200);
    }

    /**
     * @param \Modules\Notification\App\Dtos\NotificationValidatorDto $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deactivate(NotificationValidatorDto $request): JsonResponse
    {
        $notification = Notification::findOrFail($request->id);
        $this->authorize("delete", $notification);

        return response()->json($this->notificationAdminService->delete($request), 200);
    }

    /**
     * @param \Modules\Notification\App\Dtos\NotificationValidatorDto $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function activate(NotificationValidatorDto $request): JsonResponse
    {
        $notification = Notification::withTrashed()->findOrFail($request->id);
        $this->authorize("restore", $notification);

        return response()->json($this->notificationAdminService->restore($request), 200);
    }
}
