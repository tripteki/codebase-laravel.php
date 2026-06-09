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
use OpenApi\Attributes as OA;

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

    #[OA\Post(
        path: "/api/v1/auth/register",
        tags: ["UserAuth"],
        summary: "Registration",
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: "application/x-www-form-urlencoded",
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: "name", type: "string", description: "Name"),
                        new OA\Property(property: "email", type: "string", description: "Email"),
                        new OA\Property(property: "password", type: "string", description: "Password"),
                        new OA\Property(
                            property: "password_confirmation",
                            type: "string",
                            description: "Password Confirmation"
                        ),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Created."),
            new OA\Response(response: 422, description: "Validation Error."),
        ]
    )]
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
