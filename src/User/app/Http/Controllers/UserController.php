<?php

namespace Modules\User\App\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\User\App\Dtos\UserMeUpdateDto;
use Modules\User\App\Services\UserService;
use OpenApi\Attributes as OA;

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

    #[OA\Get(
        path: "/api/v1/users/me",
        tags: ["Users"],
        summary: "Show Current User Profile",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(ref: "#/components/responses/UserMeSuccess", response: 200),
            new OA\Response(ref: "#/components/responses/Unauthorized", response: 401),
            new OA\Response(ref: "#/components/responses/Forbidden", response: 403),
        ],
    )]
    #[OA\Get(
        path: "/api/v1/auth/me",
        tags: ["UserAuth"],
        summary: "Me",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(ref: "#/components/responses/UserMeSuccess", response: 200),
            new OA\Response(ref: "#/components/responses/Unauthorized", response: 401),
            new OA\Response(ref: "#/components/responses/Forbidden", response: 403),
        ],
    )]
    public function show(): JsonResponse
    {
        return response()->json($this->userService->getMe(), 200);
    }

    #[OA\Put(
        path: "/api/v1/users/me",
        tags: ["Users"],
        summary: "Update Current User Profile",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    required: ["name", "email"],
                    properties: [
                        new OA\Property(property: "name", type: "string", description: "Username"),
                        new OA\Property(property: "email", type: "string", format: "email", description: "Email address"),
                        new OA\Property(property: "full_name", type: "string", description: "Display name"),
                        new OA\Property(
                            property: "interests",
                            type: "array",
                            items: new OA\Items(type: "string"),
                            description: "Profile interests",
                        ),
                        new OA\Property(property: "password", type: "string", description: "New password"),
                        new OA\Property(property: "password_confirmation", type: "string", description: "Password confirmation"),
                    ],
                ),
            ),
        ),
        responses: [
            new OA\Response(ref: "#/components/responses/UserMeSuccess", response: 200),
            new OA\Response(ref: "#/components/responses/Unauthorized", response: 401),
            new OA\Response(ref: "#/components/responses/Forbidden", response: 403),
            new OA\Response(ref: "#/components/responses/Unvalidated", response: 422),
        ],
    )]
    /**
     * @param \Modules\User\App\Dtos\UserMeUpdateDto $userData
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserMeUpdateDto $userData, Request $request): JsonResponse
    {
        $request->validate([
            "avatar" => ["nullable", "image", "mimes:jpeg,jpg,png,gif,webp", "max:2048"],
        ]);

        return response()->json(
            $this->userService->updateMe($userData, $request->file("avatar")),
            200,
        );
    }

    #[OA\Post(
        path: "/api/v1/users/me",
        tags: ["Users"],
        summary: "Update Current User Profile (multipart)",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    required: ["name", "email"],
                    properties: [
                        new OA\Property(property: "name", type: "string", description: "Username"),
                        new OA\Property(property: "email", type: "string", format: "email", description: "Email address"),
                        new OA\Property(property: "full_name", type: "string", description: "Display name"),
                        new OA\Property(
                            property: "interests",
                            type: "array",
                            items: new OA\Items(type: "string"),
                            description: "Profile interests (e.g. interests[0], interests[1])",
                        ),
                        new OA\Property(property: "password", type: "string", description: "New password"),
                        new OA\Property(property: "password_confirmation", type: "string", description: "Password confirmation"),
                        new OA\Property(property: "avatar", type: "string", format: "binary", description: "Profile image"),
                    ],
                ),
            ),
        ),
        responses: [
            new OA\Response(ref: "#/components/responses/UserMeSuccess", response: 200),
            new OA\Response(ref: "#/components/responses/Unauthorized", response: 401),
            new OA\Response(ref: "#/components/responses/Forbidden", response: 403),
            new OA\Response(ref: "#/components/responses/Unvalidated", response: 422),
        ],
    )]
    /**
     * @param \Modules\User\App\Dtos\UserMeUpdateDto $userData
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateMultipart(UserMeUpdateDto $userData, Request $request): JsonResponse
    {
        $request->validate([
            "avatar" => ["nullable", "image", "mimes:jpeg,jpg,png,gif,webp", "max:2048"],
        ]);

        return response()->json(
            $this->userService->updateMe($userData, $request->file("avatar")),
            200,
        );
    }

    #[OA\Get(
        path: "/api/v1/users/me/interests",
        tags: ["Users"],
        summary: "Profile Interest Suggestions",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(ref: "#/components/responses/ProfileInterestsSuccess", response: 200),
            new OA\Response(ref: "#/components/responses/Unauthorized", response: 401),
            new OA\Response(ref: "#/components/responses/Forbidden", response: 403),
        ],
    )]
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function interests(): JsonResponse
    {
        return response()->json([
            "data" => $this->userService->profileInterests(),
        ], 200);
    }

    #[OA\Get(
        path: "/api/v1/users/me/accesses",
        tags: ["Users"],
        summary: "Current User Accesses",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(ref: "#/components/responses/UserAccessSuccess", response: 200),
            new OA\Response(ref: "#/components/responses/Unauthorized", response: 401),
            new OA\Response(ref: "#/components/responses/Forbidden", response: 403),
        ],
    )]
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function access(): JsonResponse
    {
        return response()->json($this->userService->access(), 200);
    }
}
