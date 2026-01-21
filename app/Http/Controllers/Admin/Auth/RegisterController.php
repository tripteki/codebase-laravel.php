<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Inertia\Inertia;
use Inertia\Response;
use Src\V1\Api\Acl\Enums\RoleEnum;
use Src\V1\Api\User\Dtos\UserDto;
use Src\V1\Api\User\Services\UserService;

class RegisterController
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
     * Show the registration form.
     *
     * @return \Inertia\Response
     */
    public function create(): Response
    {
        return Inertia::render("admin/auth/register");
    }

    /**
     * Handle an incoming registration request.
     *
     * @param \Src\V1\Api\User\Dtos\UserDto $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserDto $request)
    {
        $userService = $this->userService->create($request);

        $user = User::find($userService->id);
        $user->assignRole(RoleEnum::USER->value);

        event(new Registered($user));

        return redirect("/admin/login")->with("success", __("auth.verification-sent"));
    }
}
