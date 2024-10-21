<?php

namespace Src\V0\Notification\Http\Controllers\Admin\Notification;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use Tripteki\Notification\Contracts\Repository\Admin\INotificationRepository;
use Src\V0\Notification\Exports\Notifications\NotificationExport;
use Src\V0\Notification\Http\Requests\Admin\Notifications\NotificationShowValidation;
use Tripteki\Helpers\Http\Requests\FileExportValidation;
use App\Http\Controllers\Controller as BaseController;

class NotificationAdminController extends BaseController
{
    /**
     * @var \Tripteki\Notification\Contracts\Repository\Admin\INotificationRepository
     */
    protected $notificationAdminRepository;

    /**
     * @param \Tripteki\Notification\Contracts\Repository\Admin\INotificationRepository $notificationAdminRepository
     * @return void
     */
    public function __construct(INotificationRepository $notificationAdminRepository)
    {
        $this->notificationAdminRepository = $notificationAdminRepository;
    }

    /**
     * @OA\Get(
     *      path="/admin/notifications",
     *      tags={"Admin Notification"},
     *      summary="Index",
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="limit",
     *          description="Notification's Pagination Limit."
     *      ),
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="current_page",
     *          description="Notification's Pagination Current Page."
     *      ),
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="order",
     *          description="Notification's Pagination Order."
     *      ),
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="filter[]",
     *          description="Notification's Pagination Filter."
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success."
     *      )
     * )
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $data = [];
        $statecode = 200;

        $data = $this->notificationAdminRepository->all();

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Get(
     *      path="/admin/notifications/{notification}",
     *      tags={"Admin Notification"},
     *      summary="Show",
     *      @OA\Parameter(
     *          required=true,
     *          in="path",
     *          name="notification",
     *          description="Notification's Notification."
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success."
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not Found."
     *      )
     * )
     *
     * @param \App\Http\Requests\Admin\Notifications\NotificationShowValidation $request
     * @param string $notification
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(NotificationShowValidation $request, $notification)
    {
        $form = $request->validated();
        $data = [];
        $statecode = 200;

        $data = $this->notificationAdminRepository->get($notification);

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Get(
     *      path="/admin/notifications-export",
     *      tags={"Admin Notification"},
     *      summary="Export",
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="file",
     *          schema={"type": "string", "enum": {"csv", "xls", "xlsx"}},
     *          description="Notification's File."
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success."
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity."
     *      )
     * )
     *
     * @param \Tripteki\Helpers\Http\Requests\FileExportValidation $request
     * @return mixed
     */
    public function export(FileExportValidation $request)
    {
        $form = $request->validated();
        $data = [];
        $statecode = 200;

        if ($form["file"] == "csv") {

            $data = Excel::download(new NotificationExport(), "Notification.csv", \Maatwebsite\Excel\Excel::CSV);

        } else if ($form["file"] == "xls") {

            $data = Excel::download(new NotificationExport(), "Notification.xls", \Maatwebsite\Excel\Excel::XLS);

        } else if ($form["file"] == "xlsx") {

            $data = Excel::download(new NotificationExport(), "Notification.xlsx", \Maatwebsite\Excel\Excel::XLSX);
        }

        return $data;
    }
};
