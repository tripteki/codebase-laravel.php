<?php

namespace Modules\Auth\App\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;
use App\Http\Responses\ApiErrorResponse;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Modules\Auth\App\Dtos\AuthLoginDto;
use Modules\Auth\App\Events\UserAuthLoggedIn;
use Modules\Auth\App\Services\AuthTokenService;
use Modules\User\App\Dtos\UserTransformerDto;
use OpenApi\Attributes as OA;

class AuthenticatedController extends BaseController
{
    /**
     * @var \Modules\Auth\App\Services\AuthTokenService
     */
    protected AuthTokenService $authTokenService;

    /**
     * @param \Modules\Auth\App\Services\AuthTokenService $authTokenService
     * @return void
     */
    public function __construct(AuthTokenService $authTokenService)
    {
        $this->authTokenService = $authTokenService;
    }

    #[OA\Post(
        path: "/api/v1/auth/login",
        tags: ["UserAuth"],
        summary: "Login",
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: "application/x-www-form-urlencoded",
                schema: new OA\Schema(
                    required: ["password"],
                    properties: [
                        new OA\Property(property: "identifierKey", type: "string", description: "Credential field: email or name."),
                        new OA\Property(property: "identifierValue", type: "string", description: "Credential value when using identifierKey."),
                        new OA\Property(property: "identifier", type: "string", description: "Email or username (alternative to identifierKey/identifierValue)."),
                        new OA\Property(property: "password", type: "string", description: "Password"),
                        new OA\Property(property: "remember", type: "boolean", description: "Keep refresh token valid for the full refresh TTL."),
                    ],
                ),
            ),
        ),
        responses: [
            new OA\Response(ref: "#/components/responses/AuthTokenSuccess", response: 201),
            new OA\Response(
                response: 401,
                description: "Invalid credentials.",
                content: new OA\JsonContent(example: ["detail" => "string"]),
            ),
            new OA\Response(ref: "#/components/responses/Unvalidated", response: 422),
        ],
    )]
    /**
     * @param \Modules\Auth\App\Dtos\AuthLoginDto $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(AuthLoginDto $request): JsonResponse
    {
        $field = $request->field();
        $user = User::where($field, $request->credentialValue())->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return ApiErrorResponse::message(__("auth.invalid_credentials"), 401);
        }

        $tokens = $this->authTokenService->issue($user, $request->rememberMe());

        event(new UserAuthLoggedIn(UserTransformerDto::fromUser($user)));

        return response()->json($tokens, 201);
    }

    #[OA\Put(
        path: "/api/v1/auth/refresh",
        tags: ["UserAuth"],
        summary: "Refresh Token",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(ref: "#/components/responses/AuthTokenSuccess", response: 200),
            new OA\Response(ref: "#/components/responses/Unauthorized", response: 401),
        ],
    )]
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(): JsonResponse
    {
        return response()->json($this->authTokenService->refresh(), 200);
    }

    #[OA\Post(
        path: "/api/v1/auth/logout",
        tags: ["UserAuth"],
        summary: "Logout",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Success.",
                content: new OA\JsonContent(type: "boolean", example: true),
            ),
            new OA\Response(ref: "#/components/responses/Unauthorized", response: 401),
        ],
    )]
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(): JsonResponse
    {
        return response()->json($this->authTokenService->logout(), 200);
    }
}
