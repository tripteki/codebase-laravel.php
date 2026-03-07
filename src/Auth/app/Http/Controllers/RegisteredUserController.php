<?php

namespace Modules\Auth\App\Http\Controllers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Modules\Auth\App\Events\UserAuthRegistered;
use Modules\User\App\Dtos\UserDto;
use Modules\User\App\Services\UserService;
use Modules\Acl\App\Enums\RoleEnum;
use App\Http\Controllers\Controller as BaseController;

class RegisteredUserController extends BaseController
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
     *          description="Validation Error."
     *      )
     * )
     *
     * @param \Modules\User\App\Dtos\UserDto $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserDto $request): JsonResponse
    {
        $userService = $this->userService->create($request);

        $user = User::find($userService->id);
        $user->assignRole(RoleEnum::VISITOR->value);

        event(new Registered($user));
        event(new UserAuthRegistered($userService));

        return response()->json($userService, 201);
    }
}
