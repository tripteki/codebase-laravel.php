<?php

namespace Modules\Auth\App\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Acl\App\Enums\RoleEnum;
use Modules\Auth\App\Events\UserAuthRegistered;
use Modules\Event\App\Support\AuthAddOnsHelper;
use Modules\User\App\Dtos\UserDto;
use Modules\User\App\Services\UserService;
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
                        new OA\Property(property: "full_name", type: "string", description: "Full Name"),
                        new OA\Property(property: "email", type: "string", description: "Email"),
                        new OA\Property(property: "password", type: "string", description: "Password"),
                        new OA\Property(
                            property: "password_confirmation",
                            type: "string",
                            description: "Password Confirmation",
                        ),
                        new OA\Property(property: "tenant", type: "string", description: "Tenant slug"),
                    ],
                ),
            ),
        ),
        responses: [
            new OA\Response(response: 201, description: "Created."),
            new OA\Response(response: 422, description: "Validation Error."),
        ],
    )]
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $tenantId = $request->string("tenant")->toString() ?: null;
        AuthAddOnsHelper::initializeFromTenantId($tenantId);

        if (! AuthAddOnsHelper::isRegistrationEnabled()) {
            abort(403, __("event.add_ons.auth.registration_disabled"));
        }

        $userData = UserDto::from($request);

        $userService = $this->userService->create($userData, $tenantId);

        $user = User::query()->withoutTenancy()->find($userService->id);

        if ($user instanceof User) {
            if (! filled($tenantId) && function_exists("tenancy") && tenancy()->initialized) {
                tenancy()->end();
            }

            sync_permissions_team_context();
            $user->assignRole(RoleEnum::GUEST->value);

            if (AuthAddOnsHelper::isMailingEnabled()) {
                event(new Registered($user));
            } else {
                $user->markEmailAsVerified();
            }
        }

        event(new UserAuthRegistered($userService));

        return response()->json($userService, 201);
    }
}
