<?php

namespace Src\V0\Notification\Http\Controllers\Notification;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Tripteki\Notification\Contracts\Repository\INotificationRepository;
use Src\V0\Notification\Http\Requests\Notifications\NotificationUpdateValidation;
use Src\V0\Notification\Http\Requests\Notifications\NotificationDestroyValidation;
use App\Http\Controllers\Controller as BaseController;

class NotificationController extends BaseController
{
    /**
     * @var \Tripteki\Notification\Contracts\Repository\INotificationRepository
     */
    protected $notificationRepository;

    /**
     * @param \Tripteki\Notification\Contracts\Repository\INotificationRepository $notificationRepository
     * @return void
     */
    public function __construct(INotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * @OA\Get(
     *      path="/notifications",
     *      tags={"Notifications"},
     *      summary="Index",
     *      security={{ "bearerAuth": {} }},
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

        $this->notificationRepository->setUser($request->user());

        $data = $this->notificationRepository->all();

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Put(
     *      path="/notifications/{notification}",
     *      tags={"Notifications"},
     *      summary="Update",
     *      security={{ "bearerAuth": {} }},
     *      @OA\Parameter(
     *          required=false,
     *          in="path",
     *          name="notification",
     *          description="Notification's Notification."
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Created."
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity."
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not Found."
     *      )
     * )
     *
     * @param \App\Http\Requests\Notifications\NotificationUpdateValidation $request
     * @param string|null $notification
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(NotificationUpdateValidation $request, $notification = null)
    {
        $form = $request->validated();
        $data = [];
        $statecode = 202;

        $this->notificationRepository->setUser($request->user());

        if ($this->notificationRepository->getUser()) {

            $data = $this->notificationRepository->markAsRead($notification);

            if ($data) {

                $statecode = 201;
            }
        }

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Delete(
     *      path="/notifications/{notification}",
     *      tags={"Notifications"},
     *      summary="Destroy",
     *      security={{ "bearerAuth": {} }},
     *      @OA\Parameter(
     *          required=false,
     *          in="path",
     *          name="notification",
     *          description="Notification's Notification."
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success."
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity."
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not Found."
     *      )
     * )
     *
     * @param \App\Http\Requests\Notifications\NotificationDestroyValidation $request
     * @param string|null $notification
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(NotificationDestroyValidation $request, $notification = null)
    {
        $form = $request->validated();
        $data = [];
        $statecode = 202;

        $this->notificationRepository->setUser($request->user());

        if ($this->notificationRepository->getUser()) {

            $data = $this->notificationRepository->clear($notification);

            if ($data) {

                $statecode = 200;
            }
        }

        return iresponse($data, $statecode);
    }
};
