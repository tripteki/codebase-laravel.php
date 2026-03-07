<?php

namespace Modules\Auth\App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Modules\Auth\App\Dtos\AuthLoginDto;
use Modules\Auth\App\Events\UserAuthLoggedIn;
use Modules\Auth\App\Services\AuthTokenService;
use Modules\User\App\Dtos\UserTransformerDto;
use App\Http\Controllers\Controller as BaseController;
use App\Http\Responses\ApiErrorResponse;

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

    /**
     * @param \Modules\Auth\App\Dtos\AuthLoginDto $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(AuthLoginDto $request): JsonResponse
    {
        $field = $request->field();
        $user = User::where($field, $request->credentialValue())->first();

        if (! $user || ! \Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            return ApiErrorResponse::detail(__("auth.failed"), 401);
        }

        $tokens = $this->authTokenService->issue($user);

        event(new UserAuthLoggedIn(UserTransformerDto::fromUser($user)));

        return response()->json($tokens, 201);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(): JsonResponse
    {
        return response()->json($this->authTokenService->refresh(), 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(): JsonResponse
    {
        return response()->json($this->authTokenService->logout(), 200);
    }
}
