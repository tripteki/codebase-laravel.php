<?php

namespace Src\V1\Api\Auth\Http\Controllers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Src\V1\Api\User\Dtos\UserDto;
use Src\V1\Api\User\Services\UserService;
use Src\V1\Api\Common\Http\Controllers\Controller as BaseController;

class RegisteredUserController extends BaseController
{
    /**
     * @var \Src\V1\Api\User\Services\UserService
     */
    protected $userService;

    /**
     * @param \Src\V1\Api\User\Services\UserService $userService
     * @return void
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @OA\Post(
     *      path="/api/v1/auth/register",
     *      tags={"Auth"},
     *      summary="Registration",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="name",
     *                      type="string",
     *                      description="Name"
     *                  ),
     *                  @OA\Property(
     *                      property="email",
     *                      type="string",
     *                      description="Email"
     *                  ),
     *                  @OA\Property(
     *                      property="password",
     *                      type="string",
     *                      description="Password"
     *                  ),
     *                  @OA\Property(
     *                      property="password_confirmation",
     *                      type="string",
     *                      description="Password Confirmation"
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Created."
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity."
     *      )
     * )
     *
     * @param \Src\V1\Api\User\Dtos\UserDto $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserDto $request): JsonResponse
    {
        $userService = $this->userService->create($request);

        event(new Registered(User::find($userService->id)));

        return response()->json($userService, 201);
    }
}
