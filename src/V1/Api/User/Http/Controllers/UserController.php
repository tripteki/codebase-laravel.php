<?php

namespace Src\V1\Api\User\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Src\V1\Api\User\Dtos\UserDto;
use Src\V1\Api\User\Services\UserService;
use Src\V1\Api\Common\Http\Controllers\Controller as BaseController;

class UserController extends BaseController
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
     * @OA\Get(
     *      path="/api/v1/users",
     *      tags={"Users"},
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
        return response()->json($this->userService->all(), 200);
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
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not Found."
     *      )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(): JsonResponse
    {
        return response()->json($this->userService->get(), 200);
    }

    /**
     * @OA\Put(
     *      path="/api/v1/users",
     *      tags={"Users"},
     *      summary="Update",
     *      security={{ "bearerAuth": {} }},
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
     *                      description="E-Mail"
     *                  ),
     *                  @OA\Property(
     *                      property="password",
     *                      type="string",
     *                      description="Password"
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
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Not Authorized."
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not Found."
     *      )
     * )
     *
     * @param \Src\V1\Api\User\Dtos\UserDto $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserDto $request): JsonResponse
    {
        return response()->json($this->userService->update($request), 201);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/users",
     *      tags={"Users"},
     *      summary="Restore",
     *      security={{ "bearerAuth": {} }},
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(): JsonResponse
    {
        return response()->json($this->userService->restore(), 201);
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/users",
     *      tags={"Users"},
     *      summary="Destroy",
     *      security={{ "bearerAuth": {} }},
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(): JsonResponse
    {
        return response()->json($this->userService->delete(), 200);
    }
}
