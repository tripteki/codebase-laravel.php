<?php

namespace Src\V1\Api\Auth\Http\Controllers;

use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Src\V1\Api\Auth\Dtos\AuthLoginDto;
use Src\V1\Api\Auth\Dtos\AuthLogoutDto;
use Src\V1\Api\Common\Http\Controllers\Controller as BaseController;

class AuthenticatedController extends BaseController
{
    /**
     * @param \Src\V1\Api\Auth\Dtos\AuthLoginDto|null $request
     * @return \Src\V1\Api\Auth\Dtos\AuthLoginDto
     */
    protected function withToken(?AuthLoginDto $request = null): AuthLoginDto
    {
        $auth = Auth::guard("api");

        if ($request) {

            $token = $auth->attempt([

                $request->field => $request->identifier,
                "password" => $request->password,
            ]);

        } else {

            $token = $auth->refresh(true);
        }

        if (! $token) {

            throw ValidationException::withMessages([

                "identifier" => __("auth.failed"),
            ]);
        }

        return AuthLoginDto::from([

            "expires" => $auth->factory()->getTTL() * 60,
            "type" => "Bearer",
            "token" => $token,

        ])->except(

            "field",
            "identifier",
            "password"
        );
    }

    /**
     * @OA\Post(
     *      path="/api/v1/auth/login",
     *      tags={"Auth"},
     *      summary="Login",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="identifier",
     *                      type="string",
     *                      description="Identifier"
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
     *          response=200,
     *          description="Success."
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity."
     *      )
     * )
     *
     * @param \Src\V1\Api\Auth\Dtos\AuthLoginDto $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(AuthLoginDto $request): JsonResponse
    {
        return response()->json($this->withToken($request), 200);
    }

    /**
     * @OA\Put(
     *      path="/api/v1/auth/refresh",
     *      tags={"Auth"},
     *      summary="Refresh",
     *      security={{ "bearerAuth": {} }},
     *      @OA\Response(
     *          response=200,
     *          description="Success."
     *      )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(): JsonResponse
    {
        return response()->json($this->withToken(), 200);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/auth/logout",
     *      tags={"Auth"},
     *      summary="Logout",
     *      security={{ "bearerAuth": {} }},
     *      @OA\Response(
     *          response=200,
     *          description="Success."
     *      )
     * )
     *
     * @param \Src\V1\Api\Auth\Dtos\AuthLogoutDto $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(AuthLogoutDto $request): JsonResponse
    {
        return response()->json(Auth::guard("api")->logout(true), 200);
    }
}
