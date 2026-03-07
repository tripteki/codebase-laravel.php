<?php

namespace Modules\User\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Modules\User\App\Dtos\UserDto;
use Modules\User\App\Services\UserService;
use App\Http\Controllers\Controller as BaseController;

class UserController extends BaseController
{
    /**
     * @var \Modules\User\App\Services\UserService
     */
    protected $userService;

    /**
     * @param \Modules\User\App\Services\UserService $userService
     * @return void
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/users/me",
     *      tags={"Users"},
     *      summary="Show",
     *      security={{ "bearerAuth": {} }},
     *      @OA\Response(
     *          response=200,
     *          description="Success."
     *      )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(): JsonResponse
    {
        return response()->json($this->userService->get(), 200);
    }
}
